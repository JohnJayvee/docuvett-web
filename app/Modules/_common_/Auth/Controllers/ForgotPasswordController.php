<?php

namespace App\Modules\_common_\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Models\User\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $user = User::where('email', 'like', $request->email)->first();
        if ($user) {
            $token = urlencode($this->broker()->createToken($user));
            $email = urlencode($user->email);
            $link = url("password-reset?email={$email}&token={$token}");

            \Mail::send(new PasswordReset($user, $link));

            return response()->json([
                'message' => "Successfully sent. Please, check your email"
            ]);
        }
        return response()->json([
            'message' => "This email doesn't match any user"
        ], 404);
    }

    public function broker()
    {
        return Password::broker();
    }
}
