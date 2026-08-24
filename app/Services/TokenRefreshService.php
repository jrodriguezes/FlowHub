<?php

namespace App\Services;

use App\Models\ServiceConnection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class TokenRefreshService
{
    public function refreshTokenIfNeeded(ServiceConnection $connection)
    {
        // we verify if its close(in the 5mins or less) to expire or already expired
        if (!$connection->expires_at || !now()->parse($connection->expires_at)->subMinutes(5)->isPast()) {
            return; // the token is ok, we don't need to refresh it
        }

        // here we start the logic to refresh the token
        if (!$connection->refresh_token) {
            return; // impossible to refresh the token without a refresh_token
        }

        //  we use the refresh to get the new tokens
        // we get a padlock for 10 secs in redis 
        // if two workers arrive at the same time, just 1 will open the padlock
        $lock = Cache::lock('refresh_token_lock_' . $connection->id, 10);

        if ($lock->get()) {
            try {
                // since a few milisencond have passed, we check the database again
                $connection->refresh();
                if (!now()->parse($connection->expires_at)->subMinutes(5)->isPast()) {
                    return;
                }

                $this->performRefresh($connection);
            } finally {
                // unlock the padlock no matter what happens
                $lock->release();
            }
        } else {
            // if the padlock was closed, it means that another worker is in the process of refreshing the token
            // instead of colliding with him, we wait patiently for 2 seconds and reload the fresh data from the DB.
            sleep(2);
            $connection->refresh();
        }
    }

    // this is the function that really speaks with GOOGLE
    private function performRefresh(ServiceConnection $connection): void
    {
        if ($connection->provider === 'google') {

            // here is the direct HTTP call to Google's servers
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => $connection->refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            // if google says everything went well (HTTP 200 OK)
            if ($response->successful()) {
                $data = $response->json();

                // we save the new access token in our database
                $connection->update([
                    'access_token' => $data['access_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
                    // we save the refresh token if google doesn't send a new one
                    'refresh_token' => $data['refresh_token'] ?? $connection->refresh_token,
                ]);
            } else {
                // if google rejects the request
                $errorData = $response->json();

                // Mark connection as 'revoked' when Google responds with 'invalid_grant'
                if (isset($errorData['error']) && $errorData['error'] === 'invalid_grant') {
                    $connection->update(['status' => 'revoked']);
                    Log::warning("Token revocado permanentemente por Google para la conexión {$connection->id}");
                } else {
                    Log::error('Fallo grave al intentar refrescar el token de Google', $errorData ?? []);
                }
            }
        }
    }
}