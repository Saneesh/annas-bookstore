<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class EnsureCorrectAPIHeaders
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
        if($request->headers->get('accept') !== 'application/vnd.api+json'){
            return $this->addCorrectContentType(new Response('', 406));
        }

        if($request->isMethod('POST') || $request->isMethod('PATCH')){
            if($request->header('content-type') !== 'application/vnd.api+json'){
                return $this->addCorrectContentType(new Response('', 415));
            }
        }
        
        return $this->addCorrectContentType($next($request));
    }

    /**
     * @param  Symfony\Component\HttpFoundation\Response $request
     * By referencing the parent class in the argument, we 
     * are able to pass in any class that inherits 
     * from the Symfony\Component\HttpFoundation\Response class. 
     * This means that a Illuminate\Foundation\Testing\TestResponse we use 
     * when we are testing our API, as well as regular 
     * Illuminate\Http\Response, can be passed in since both of these are 
     * inheriting from Symfony\Component\HttpFoundation\Response.
     */
    private function addCorrectContentType(BaseResponse $response)
    {
        $response->headers->set('content-type', 'application/vnd.api+json');
        return $response;
    }
}
