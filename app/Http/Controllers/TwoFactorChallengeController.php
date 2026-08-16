<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;

class TwoFactorChallengeController extends Controller
{
    public function show()
    {
        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }
        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $userId = session('2fa:user:id');
        $user = User::findOrFail($userId);

        // decrypt the user's secret code
        $secret = Crypt::decryptString($user->two_factor_secret);

        $google2fa = new Google2FA();

        $isValid = $google2fa->verifyKey($secret, $request->code);

        if ($isValid) {
            Auth::login($user);
            $request->session()->regenerate();
            session()->forget('2fa:user:id');
            return redirect()->intended('home');
        }

        return back()->withErrors([
            'code' => 'Invalid code',
        ])->onlyInput('code');
    }
}
