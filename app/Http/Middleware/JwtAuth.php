<?php

namespace App\Http\Middleware;
use \Firebase\JWT\JWT;
use Closure;
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
      $key = 'keyPair';

      $token= $request->input('jwtToken');
 
       try{
         
         $decoded = JWT::decode($token, $key, array('HS256'));
         return $next($request);
       
       }
       catch(\Exception $e){
         // echo 'FAILED';
         echo $e->getMessage();
       }

        // return response()->json($request->all());
    }

    // public function __construct(Request $request)
    // {
    //     $this->token = $request->all();
    //     $this->jwt = JWT::encode($this->token,$this->key);
    // }

    //

    public function verifyToken($request){
      $key = 'heilHitler';

     $token= $request->input('jwtToken');

      try{
        
        $decoded = JWT::decode($token, 'Fake', array('HS256'));
        return $decoded;
      
      }
      catch(\Exception $e){
        // echo 'FAILED';
        echo $e->getMessage();
      }
       
      
    }

    public function generateToken($user){
      $key = 'keyPair';
     
      $date = new \DateTime();
      $date->add(new \DateInterval('P1D'));
      // $date->add(new DateInterval('P1D'));
      $token = array(
        "iss"=>"pcogd",
        "sub"=> $user->id,
        "aud"=> "pcogdfm",
        "exp"=> intval($date->format('U')),
        "iat"=>time()
      );

      $jwt = JWT::encode($token,$key);

      return $jwt;
      
    }
}
