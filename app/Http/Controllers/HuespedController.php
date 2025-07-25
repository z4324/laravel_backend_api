<?php

namespace App\Http\Controllers;

use App\Models\Huesped;
use App\Models\Sesion;
use App\Models\CodigoSeguridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class HuespedController extends Controller
{
    public function list()
{
    return response()->json(Huesped::all());
}
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
            'rol' => 'huesped',
            'contrasena' => Hash::make($request->contrasena),
            'fecha_registro' => now(),
        ]);

        return response()->json($huesped, 201);
    }

        public function editarPerfil(Request $request)
{
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'nombre'     => 'sometimes|required|string',
        'apellidos'  => 'sometimes|required|string',
        'telefono'   => 'sometimes|required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $datos = [];

    if ($request->has('nombre')) {
        $datos['nombre'] = ucwords(strtolower($request->nombre));
    }
    if ($request->has('apellidos')) {
        $datos['apellidos'] = ucwords(strtolower($request->apellidos));
    }
    if ($request->has('telefono')) {
        $datos['telefono'] = $request->telefono;
    }

    $user->update($datos);

    return response()->json([
        'message' => 'Perfil actualizado correctamente.',
        'huesped' => $user->fresh()
    ]);
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

        if (!$huesped || !Hash::check($request->contrasena, $huesped->contrasena)) {
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

    public function actualizarContrasena(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email|exists:huespedes,correo',
            'codigo' => 'required|numeric',
            'nueva_contrasena' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $huesped = Huesped::where('correo', $request->correo)->first();

        $codigoDB = CodigoSeguridad::where('huesped_id', $huesped->id)
            ->where('codigo', $request->codigo)
            ->where('expires_at', '>', now())
            ->first();

        if (!$codigoDB) {
            return response()->json(['error' => 'Código incorrecto o expirado.'], 400);
        }

        $huesped->contrasena = Hash::make($request->nueva_contrasena);
        $huesped->save();

        $codigoDB->delete();

        $tokens = $huesped->tokens()->pluck('id');
        PersonalAccessToken::whereIn('id', $tokens)->delete();
        Sesion::where('user_id', $huesped->id)->update(['estado' => 'inactiva']);

        return response()->json([
            'message' => 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión.',
            'redirigir' => '/login'
        ]);
    }
    public function newPassword(Request $request)
{
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'contrasena_actual' => 'required|string',
        'nueva_contrasena' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    if (!Hash::check($request->contrasena_actual, $user->contrasena)) {
        return response()->json(['error' => 'La contraseña actual es incorrecta.'], 400);
    }

    $user->contrasena = Hash::make($request->nueva_contrasena);
    $user->save();

    return response()->json(['message' => 'Contraseña actualizada correctamente.']);
}
}
