<?php

namespace App\Modules\_common_\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Utils\Utils;
use App\Models\User\User;
use Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Twilio\Rest\Client;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout', 'refresh');
    }

    public function login(Request $request)
    {
        if ($request->has('token')) {
            $token = $request->token;
            JWTAuth::setToken($token);
        } else {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');

            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json([
                        'error' => 'Invalid credentials'
                    ], 401);
                }
            } catch (JWTException $e) {
                return response()->json([
                    'error' => 'Could not create token'
                ], 500);
            }
        }

        $user = Utils::getCurrentUser();
        $twoFaKey = '2fa_code_' . $user->id;
        if (!$request->has('code')) {
            Cache::forget($twoFaKey);
        }

        if ($user->two_factor_auth) {
            if (!$cacheKey = Cache::get($twoFaKey)) {
                $code = $this->generateCode();
                $this->storeCode($twoFaKey, $code);
                $this->sendCode($user->id, $user->phone, $code);

//todo check work

//                $cacheKey = Cache::get($twoFaKey);
//            }
//            $this->validate($request, [
//                'code' => [
//                    function ($attribute, $value, $fail) use ($cacheKey, $request) {
//                        if (!isset($request->code)) {
//                            $fail('Please enter verification code');
//                        } elseif (!$cacheKey && $request->code) {
//                            $fail('Verification code is expired');
//                        } elseif ($cacheKey != $request->code) {
//                            $fail('Verification code is invalid.');
//                        }
//                    },
//                    'required'
//                ]
//            ]);

            }
            $this->validate(
                $request,
                [
                    'code' => [
                        function ($attribute, $value, $fail) use ($cacheKey, $request, $twoFaKey) {
                            if (!$cacheKey) {
                                $fail([
                                    'Verification code is expired',
                                    'Verification code outdated. The new verification code was successfully sent again. Please enter the new code.'
                                ]);
                            } elseif (Cache::get($twoFaKey) != $request->code && !($request->has('code') && empty($request->code)) ) {
                                $fail('Verification code is invalid.');
                            }
                        },
                        'required'
                    ]
                ],
                ['required' => !$request->has('code') ? 'Verification code was successfully sent' : 'Please enter verification code']
            );
            Cache::forget($twoFaKey);
        }

        $showMessages = $user->isAbleTo('system-messages.list');

        return response()
            ->json([
                'token' => "Bearer $token",
                'system_messages' => $showMessages
            ])
            ->header('Authorization', $token);
    }

    private function generateCode($codeLength = 4)
    {
        $min = pow(10, $codeLength);
        $max = $min * 10 - 1;
        $code = mt_rand($min, $max);

        return $code;
    }

    /**
     * @param string $twoFaKey
     * @param int $code
     */
    private function storeCode($twoFaKey, $code)
    {
        Cache::put($twoFaKey, $code, Carbon::now()->addMinutes(15));
    }

    private function sendCode($userId, $phone, $code)
    {
        try {
            $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            $twilio->messages->create($phone,
                [
                    'from' => config('services.twilio.sender_number'),
                    'body' => $code . ' is your ' . config('app.name') . ' verification code'
                ]
            );
        } catch (Exception $e) {
            Log::error('Fail on sending 2FA code. Phone: ' . $phone . '. Code: ' . $code . '. Error: ' . $e->getMessage());
            return false; //enable to send SMS
        }
        return true;
    }

    public function logout(): void
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function refresh(): Response
    {
        return response()
            ->noContent(200)
            ->withHeaders([
                'Authorization' => 'Bearer ' . JWTAuth::manager()->setBlacklistEnabled(false)->refresh(JWTAuth::getToken())
            ]);
    }
}
