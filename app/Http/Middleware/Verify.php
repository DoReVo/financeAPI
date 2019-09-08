<?php

namespace App\Http\Middleware;

use Closure;

class Verify
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

        try {
            if ($request->test!='pass') {
                return response($request->input('id').' Is your id', 404);
            }
        } catch (\Throwable $th) {
            return response($th->getMessage());
        }
        

        return $next($request);
    }
}
