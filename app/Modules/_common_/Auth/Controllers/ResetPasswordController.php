<?php

namespace App\Modules\_common_\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function passwordReset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'token' => 'required',
            'password' => RegisterController::$rules['password'],
        ]);

        $credentials = $this->credentials($request);

        $response = $this->broker()->reset(
            $credentials, function ($user, $password) {
                $user->password = $password;
                $user->audit_tags = ['Password Reset'];
                $user->save();
            }
        );

        $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);


        if ($response == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($response)
            ]);
        } else {
            return response()->json([
                'message' => __($response)
            ], 422);
        }


    }

    public function broker()
    {
        return Password::broker();
    }
}
