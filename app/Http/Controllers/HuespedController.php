<?php

namespace App\Http\Controllers;

use App\Models\Huesped;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HuespedController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'apellidos' => 'required|string',
            'telefono' => 'required|string',
            'correo' => 'required|email|unique:huespedes,correo',
            'contrasena' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $huesped = Huesped::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'fecha_registro' => now(),
        ]);

        return response()->json($huesped, 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $huesped = Huesped::where('correo', $request->correo)->first();

        if (!$huesped || !\Hash::check($request->contrasena, $huesped->contrasena)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $tokenInstance = $huesped->createToken('huesped_token');
        $token = $tokenInstance->plainTextToken;
        $tokenId = $tokenInstance->accessToken->id; 

        Sesion::create([
            'user_id'    => $huesped->id,
            'token'      => $token,
            'token_id'   => $tokenId, 
            'user_agent' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'inicio'     => now(),
            'estado'     => 'activa',
        ]);

        return response()->json([
            'huesped' => $huesped,
            'token'   => $token,
        ]);
    }

    public function list()
    {
        return response()->json(Huesped::all());
    }
}