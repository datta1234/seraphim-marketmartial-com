<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class MfaController extends Controller
{
    public function index(Request $request) {
        return view('google2fa.index'); 
    }

    public function setup(Request $request)
    {
        // Initialise the 2FA class
        $google2fa = app('pragmarx.google2fa');

        // Add the secret key to the session
        $google2fa_secret = $google2fa->generateSecretKey();
        session(['google2fa_secret' => $google2fa_secret]);

        // Generate the QR image. This is the image the user will scan with their app
        // to set up two factor authentication
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            \Auth::user()->email,
            $google2fa_secret
        );

        // Pass the QR barcode image to the view
        return view('google2fa.setup', ['QR_Image' => $QR_Image, 'secret' => $google2fa_secret]);
    }

    public function finishSetup(Request $request) {

        //save 2fa secret key to the db
        $google2fa_secret = session('google2fa_secret');
        \Auth::user()->update(['google2fa_secret' => $google2fa_secret]);
        return redirect()->route('admin.user.index')->with('success', 'MFA setup completed!');
    }
}    