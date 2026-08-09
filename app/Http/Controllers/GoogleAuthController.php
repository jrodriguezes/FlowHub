<?php

namespace App\Http\Controllers;

use App\Models\ServiceConnection;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        // here we tell to google which permissions we want
        return Socialite::driver('google')->scopes([
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/calendar.events'
        ])->with([
                    // this is important because we tell to google that we need ofline access
                    // to send emails while the user isnt active in the app.
                    // 'prompt' => 'consent' is important to ask the user to give permissions again
                    'access_type' => 'offline',
                    'prompt' => 'consent'
                ])->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            // socialite get the code in the url and exchanges for the user and the user token
            $googleUser = Socialite::driver('google')->user();
            // we search if the user had the google account created if not, we are going to create it 
            ServiceConnection::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'provider' => 'google'
                ],
                [
                    'external_id' => $googleUser->getId(),
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken,
                    'scopes' => $googleUser->approvedScopes ?? [],
                    'expires_at' => now()->addSeconds($googleUser->expiresIn),
                    'status' => 'active',
                ]
            );

            return redirect()->route('connections.index');
        } catch (\Exception $e) {
            return redirect()->route('connections.index')->with('error', 'Error al conectar con Google');
        }
    }
}
