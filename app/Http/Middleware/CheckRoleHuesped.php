<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleHuesped
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || $user->rol !== 'huesped') {
            return response()->json(['error' => 'No autorizado. Solo usuarios huesped.'], 403);
        }
        return $next($request);
    }
}