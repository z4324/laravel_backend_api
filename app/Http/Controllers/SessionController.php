<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class SessionController extends Controller
{
public function index(Request $request)
{
    $userId = Auth::id();
    $currentToken = $request->bearerToken();

    $sesion = Sesion::where('user_id', $userId)
        ->where('token', $currentToken)
        ->where('estado', 'activa')
        ->first();

    if (!$sesion) {
        return response()->json(['message' => 'Sesión inválida.'], 401);
    }

    return response()->json([
        'fecha_inicio' => $sesion->inicio,
        'ip' => $sesion->ip_address,
    ]);
}


    public function destroy($id)
    {
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['message' => 'Sesión no encontrada.'], 404);
        }

        if ($sesion->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $sesion->estado = 'inactiva';
        $sesion->save();

        if ($sesion->token_id) {
            PersonalAccessToken::where('id', $sesion->token_id)->delete();
        }

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
    

    public function destroyAllExceptCurrent(Request $request)
    {
        $userId = Auth::id();
        $currentToken = $request->bearerToken();

        if (!$currentToken) {
            return response()->json(['message' => 'Token no encontrado.'], 400);
        }

        $sesiones = Sesion::where('user_id', $userId)
            ->where('token', '!=', $currentToken)
            ->where('estado', 'activa')
            ->get();

        $cerradas = 0;
        foreach ($sesiones as $sesion) {
            $sesion->estado = 'inactiva';
            $sesion->save();
            
            if ($sesion->token_id) {
                PersonalAccessToken::where('id', $sesion->token_id)->delete();
            }
            $cerradas++;
        }

        return response()->json([
            'message' => "Se cerraron {$cerradas} sesiones.",
            'sesiones_cerradas' => $cerradas
        ]);
    }

    public function destroyCurrent(Request $request)
    {
        $userId = Auth::id();
        $currentToken = $request->bearerToken();

        if (!$currentToken) {
            return response()->json(['message' => 'Token no encontrado.'], 400);
        }

        $sesion = Sesion::where('user_id', $userId)
            ->where('token', $currentToken)
            ->where('estado', 'activa')
            ->first();

        if ($sesion) {
            $sesion->estado = 'inactiva';
            $sesion->save();
            
            if ($sesion->token_id) {
                PersonalAccessToken::where('id', $sesion->token_id)->delete();
            }
        }

        return response()->json(['message' => 'Sesión actual cerrada.']);
    }
    public function destroyAll(Request $request)
{
    $userId = Auth::id();

    Sesion::where('user_id', $userId)->delete();

    return response()->json(['message' => 'Todas las sesiones eliminadas.']);
}

}