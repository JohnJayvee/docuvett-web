<?php

namespace App\Modules\_common_\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Utils\Utils;
use App\Mail\UserRegistered;
use App\Models\User\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    public static $rules = [
        'name'     => 'required',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:6',
        'phone'    => 'phone:AUTO,AU|unique:users,phone',
        'radius'   => 'numeric|min:1',
    ];

    public static $rulesContact = [
        'name'     => 'required',
        'email'    => 'required|email|unique:users,email',
        'phone'    => 'phone:AUTO,AU|unique:users,phone',
    ];

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        if (!config('app.enable.registration')) {
            return response()->json([
                'message' => 'Registration not available'
            ], 405);
        }
        $this->internalValidateData($request);
        $data = $request->all();

        $user = User::create($data);
        $user->syncRoles(['customer']);

        \Mail::send(new UserRegistered($user, $data['password'], true));

        return response()->json([
            'message' => 'Successfully registered'
        ]);
    }

    /**
     * @param Request $request
     */
    public function internalValidateData(Request $request)
    {
        $request       = Utils::updateRequestPhone($request);
        $rules         = self::$rules;
        $this->validate($request, $rules);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateData(Request $request)
    {
        if (!config('app.enable.registration')) {
            return response()->json([
                'message' => 'Registration not available'
            ], 405);
        }

        $this->internalValidateData($request);

        return response()->json([
            'message' => 'Successfully registered'
        ]);
    }
}
