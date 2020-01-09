<?php

namespace App\Http\Middleware;

use Closure;
use \Firebase\JWT\JWT;

// use \DateTime;
// use \DateInterval;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        // Public Key from env
        $publicKey = getenv('JWT_PUBLIC_KEY');

        $token = $request->bearerToken();

        if (!$token) {
            return response(
                json_encode(
                    array(
                        'message' => 'Unauthorized request',
                    )
                ),
                401
            );
        }

        try {
            $decoded = JWT::decode($token, $publicKey, array('RS256'));
            return $next($request);
        } catch (\Exception $e) {
            return response(
                json_encode(
                    array(
                        'message' => 'No permission',
                    )
                ),
                403
            );

        }
    }

    public function generateToken($user)
    {
        $key = 'keyPair';

        $date = new \DateTime();
        $date->add(new \DateInterval('P1D'));
        // $date->add(new DateInterval('P1D'));
        $token = array(
            "iss" => "pcogd",
            "sub" => $user->id,
            "aud" => "pcogdfm",
            "exp" => intval($date->format('U')),
            "iat" => time(),
        );

        $jwt = JWT::encode($token, $key);

        return $jwt;
    }
}
