<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class SessionController extends Controller
{
    private function getCurrentUser()
    {
        if (Auth::guard('admin-sanctum')->check()) {
            return Auth::guard('admin-sanctum')->user();
        }
        
        if (Auth::guard('sanctum')->check()) {
            return Auth::guard('sanctum')->user();
        }
        
        return null;
    }

    private function getCurrentUserId()
    {
        $user = $this->getCurrentUser();
        return $user ? $user->id : null;
    }

    public function index(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $currentToken = $request->bearerToken();

        if (!$userId) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

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
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['message' => 'Sesión no encontrada.'], 404);
        }

        if ($sesion->user_id !== $userId) {
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
        $userId = $this->getCurrentUserId();
        $currentToken = $request->bearerToken();

        if (!$userId) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

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
        $userId = $this->getCurrentUserId();
        $currentToken = $request->bearerToken();

        if (!$userId) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

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
        $userId = $this->getCurrentUserId();

        if (!$userId) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

        Sesion::where('user_id', $userId)->delete();

        return response()->json(['message' => 'Todas las sesiones eliminadas.']);
    }

}