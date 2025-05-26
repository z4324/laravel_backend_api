<?php

namespace App\Http\Controllers;

use App\Models\Huesped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HuespedController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|string|email|max:255|unique:huespedes,correo',
            'contrasena' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $huesped = Huesped::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'fecha_registro' => now(),
        ]);

        if ($huesped && $huesped->_id) {
            return response()->json(['ok' => true, 'huesped' => $huesped]);
        } else {
            return response()->json(['ok' => false, 'msg' => 'No se insertó el registro'], 500);
        }
    }
    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'correo' => 'required|email',
        'contrasena' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $huesped = Huesped::where('correo', $request->correo)->first();

    if (!$huesped || !Hash::check($request->contrasena, $huesped->contrasena)) {
        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }

    return response()->json(['ok' => true, 'user' => [
        'id' => (string)$huesped->_id,
        'nombre' => $huesped->nombre,
        'apellidos' => $huesped->apellidos,
        'telefono' => $huesped->telefono,
        'correo' => $huesped->correo,
        'fecha_registro' => $huesped->fecha_registro,
    ]]);
}
public function list()
{
    $huespedes = \App\Models\Huesped::all(['_id', 'nombre']);
    $result = $huespedes->map(function($h){
        return [
            'id' => (string)$h->_id,
            'nombre' => $h->nombre
        ];
    });
    return response()->json($result);
}
}