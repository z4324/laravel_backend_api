<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Huesped;
use App\Models\CodigoSeguridad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CodigoSeguridadMail;
use Carbon\Carbon;

class CodigoSeguridadController extends Controller
{
public function enviarCodigo(Request $request)
{
    $request->validate([
        'correo' => 'required|email',
    ]);

    $huesped = Huesped::where('correo', $request->correo)->first();

    if (!$huesped) {
        return response()->json(['success' => false], 200);
    }

    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    CodigoSeguridad::updateOrCreate(
        ['huesped_id' => $huesped->id],
        [
            'codigo' => $codigo,
            'expires_at' => now()->addMinutes(10)
        ]
    );

    Mail::to($huesped->correo)->send(new CodigoSeguridadMail($codigo));

    return response()->json(['success' => true], 200);
}

public function validarCodigo(Request $request)
{
    $request->validate([
        'correo' => 'required|email|exists:huespedes,correo',
        'codigo' => 'required|string|size:6',
    ]);

        $huesped = Huesped::where('correo', $request->correo)->first();

        $codigoDB = CodigoSeguridad::where('huesped_id', $huesped->id)
            ->where('codigo', $request->codigo)
            ->where('expires_at', '>', now())
            ->first();

        if (!$codigoDB) {
            return response()->json(['error' => 'Código incorrecto o expirado.'], 400);
        }


    return response()->json(['message' => 'Código válido.']);
}


    
}