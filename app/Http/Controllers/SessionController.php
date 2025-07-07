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
            ->get();
        return response()->json($sesiones);
    }

    public function destroy($id)
    {
        $sesion = Sesion::find($id);

        if ($sesion && $sesion->user_id === Auth::id()) {
            $sesion->estado = 'inactiva';
            $sesion->save();

            PersonalAccessToken::where('id', $sesion->token_id)->delete();

            return response()->json(['message' => 'Sesión cerrada.']);
        }

        return response()->json(['message' => 'No autorizado o sesión no encontrada.'], 404);
    }

    public function destroyAllExceptCurrent(Request $request)
    {
        $userId = Auth::id();
        $currentToken = $request->bearerToken();

        $sesiones = Sesion::where('user_id', $userId)
            ->where('token', '!=', $currentToken)
            ->where('estado', 'activa')
            ->get();

        foreach ($sesiones as $sesion) {
            $sesion->estado = 'inactiva';
            $sesion->save();
            PersonalAccessToken::where('id', $sesion->token_id)->delete();
        }

        return response()->json(['message' => 'Otras sesiones cerradas.']);
    }
}