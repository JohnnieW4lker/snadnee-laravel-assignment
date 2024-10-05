<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowAuthenticatedOnly
{
    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() === null) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
