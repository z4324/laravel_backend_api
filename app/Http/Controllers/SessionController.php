<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class SessionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $sesiones = Sesion::where('user_id', $userId)
            ->where('estado', 'activa')
            ->orderBy('inicio', 'desc')
            ->get();
        
        $sesiones->each(function ($sesion) {
            $sesion->fecha_inicio = $sesion->inicio;
            $sesion->ip = $sesion->ip_address;
        });
        
        return response()->json($sesiones);
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
}