<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Sesion;
use App\Models\Huesped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AdminController extends Controller
{
        public function list()
    {
        return response()->json(\App\Models\Huesped::all());
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = Admin::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'rol' => 'admin',
            'password' => Hash::make($request->password),
            'fecha_registro' => now(),
        ]);

        return response()->json($admin, 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $tokenInstance = $admin->createToken('admin_token');
        $token = $tokenInstance->plainTextToken;
        $tokenId = $tokenInstance->accessToken->id; 

        Sesion::create([
            'user_id'    => $admin->id,
            'token'      => $token,
            'token_id'   => $tokenId, 
            'user_agent' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'inicio'     => now(),
            'estado'     => 'activa',
        ]);

        return response()->json([
            'token'      => $token,
            'admin'      => $admin,
            'token_type' => 'Bearer',
            'user_type'  => 'admin'
        ]);
    }

    public function editarPerfil(Request $request)
    {
        $user = Auth::guard('admin-sanctum')->user();

        $validator = Validator::make($request->all(), [
            'nombre'     => 'sometimes|required|string',
            'apellidos'  => 'sometimes|required|string',
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

        $user->update($datos);

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'admin' => $user->fresh()
        ]);
    }

    public function newPassword(Request $request)
    {
        $user = Auth::guard('admin-sanctum')->user();

        $validator = Validator::make($request->all(), [
            'password_actual' => 'required|string',
            'nueva_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Hash::check($request->password_actual, $user->password)) {
            return response()->json(['error' => 'La contraseña actual es incorrecta.'], 400);
        }

        $user->password = Hash::make($request->nueva_password);
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }
}
