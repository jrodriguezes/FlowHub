<?php

namespace App\Http\Controllers;

use App\Enums\ConnectionStatus;
use App\Models\ServiceConnection;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller
{
    /**
     * @var list<string>
     */
    private array $scopes = [
        'read:user',
        'public_repo',
    ];

    public function redirect()
    {
        return Socialite::driver('github')
            ->scopes($this->scopes)
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $connection = ServiceConnection::query()->firstOrNew([
                'user_id' => $request->user()->id,
                'provider' => 'github',
            ]);

            $connection->fill([
                'external_id' => $githubUser->getId(),
                'access_token' => $githubUser->token,
                'scopes' => $githubUser->approvedScopes ?? $this->scopes,
                'expires_at' => $githubUser->expiresIn
                    ? now()->addSeconds($githubUser->expiresIn)
                    : null,
                'status' => ConnectionStatus::ACTIVE,
                'revoked_at' => null,
            ]);

            if (filled($githubUser->refreshToken)) {
                $connection->refresh_token = $githubUser->refreshToken;
            }

            $connection->save();

            return redirect()
                ->route('connections.index')
                ->with('success', 'Cuenta de GitHub conectada.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('connections.index')
                ->with('error', 'No se pudo conectar con GitHub.'.(config('app.debug') ? ' '.$e->getMessage() : ''));
        }
    }
}
