<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use PDOException;

class DashboardDatabaseCatchMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (QueryException|PDOException $e) {
            return response()->view('proxy-error', [
                'message' => 'Dashboard xxx temporariamente indisponível',
                'code' => 502
            ], 502);
        }
    }
}

