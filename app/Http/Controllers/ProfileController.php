<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $qrCodeUrl = null;

        // if user has a secret but not confirmed or used yet(which means that the user just enabled 2fa) 
        // we show him the qr code to confirm the 2fa
        if ($user->two_factor_secret && !$user->two_factor_confirmed) {
            $google2fa = new Google2FA();
            $secret = Crypt::decryptString($user->two_factor_secret);

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                'FlowHub',
                $user->email,
                $secret
            );

        }
        return view('profile.index', compact('qrCodeUrl'));

    }

    public function enable(Request $request)
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $request->user()->update([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_confirmed' => false
        ]);

        return back()->with('status', '2fa-enabled');
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);
        $user = $request->user();
        $google2fa = new Google2FA();
        $secret = Crypt::decryptString($user->two_factor_secret);
        $isValid = $google2fa->verifyKey($secret, $request->code);
        if ($isValid) {
            $user->update([
                'two_factor_confirmed' => true
            ]);

            return back()->with('status', '2fa-confirmed');
        }

        return back()->withErrors(['code' => 'El código es inválido.']);
    }

    public function disable(Request $request)
    {
        $request->user()->update([
            'two_factor_secret' => null,
            'two_factor_confirmed' => false
        ]);

        return back()->with('status', '2fa-disabled');
    }

}

