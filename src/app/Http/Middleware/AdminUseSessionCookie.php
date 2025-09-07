<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUseSessionCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $cookieConfigKey = 'cookie'): Response
    {
        // StartSession 実行前にクッキー名を上書き
        config(['session.cookie' => config("session.{$cookieConfigKey}")]);
        return $next($request);
    }
}
