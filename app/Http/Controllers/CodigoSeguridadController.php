<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CodigoSeguridadMail;
use App\Models\CodigoSeguridad;
use Carbon\Carbon;

class CodigoSeguridadController extends Controller
{
    public function enviarCodigo(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        CodigoSeguridad::updateOrCreate(
            ['huesped_id' => $user->id],
            [
                'codigo' => $codigo,
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        Mail::to($user->correo)->send(new CodigoSeguridadMail($codigo));

        return response()->json([
            'message' => 'Código enviado al correo del huésped.'
        ]);
    }

    public function validarCodigoYCambiarPassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'codigo' => 'required|string|size:6',
            'nueva_contrasena' => 'required|string|min:6',
        ]);

        $codigoDB = CodigoSeguridad::where('huesped_id', $user->id)
            ->where('codigo', $request->codigo)
            ->where('expires_at', '>', now())
            ->first();

        if (!$codigoDB) {
            return response()->json(['error' => 'Código inválido o expirado'], 422);
        }

        $user->contrasena = \Hash::make($request->nueva_contrasena);
        $user->save();

        $codigoDB->delete();

        $tokens = $user->tokens()->pluck('id');
        \Laravel\Sanctum\PersonalAccessToken::whereIn('id', $tokens)->delete();
        \App\Models\Sesion::where('user_id', $user->id)->update(['estado' => 'inactiva']);

        return response()->json([
            'message' => 'Contraseña actualizada correctamente.',
            'cerrar_sesion' => true
        ]);
    }
}