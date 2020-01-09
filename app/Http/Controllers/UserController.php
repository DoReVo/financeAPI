<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // $this->validate($request, ['email' => ['required','email']]);
    }

    public function login(Request $request)
    {
        $user = new User;

        // Find user with matching username
        $user = $user->where([
            ['username', '=', $request->username],

        ])->first();

        // If user not found
        if (!$user) {
            return response(json_encode(array('message' => 'Wrong username or password')), 400);
        }

        // If password is wrong
        if (!password_verify($request->password, $user->password)) {
            return response(
                json_encode(
                    array(
                        'message' => 'Wrong username or password',
                    )
                ),
                400
            );
        };

        // Private key from env
        $privateKey = getenv('JWT_PRIVATE_KEY');

        // JWT claims
        $token = array(
            'iss' => 'myMoney',
            'sub' => $user->id,
        );

        $jwtHandler = new JWT;

        // Encode into jwt
        $jwtToken = $jwtHandler->encode($token, $privateKey, 'RS256');

        return response(
            json_encode(
                array(
                    'token' => $jwtToken,
                    'id' => $user->id,
                    'username' => $user->username,
                )
            ),
            200
        );
    }
}
