<?php

namespace App\Http\Controllers;

use App\Models\Multa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MultaController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'huesped_id' => 'required|exists:huespedes,id',
            'monto' => 'required|numeric',
            'motivo' => 'required|string',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:pendiente,pagada',
            'fecha_notificacion' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $request->merge([
        'fecha_notificacion' => now()
        ]);


        $multa = Multa::create($request->all());
        return response()->json($multa, 201);
    }

    public function multasPorHuesped($id)
    {
        $multas = Multa::where('huesped_id', $id)->get();
        return response()->json($multas);
    }

    public function marcarComoVista($id)
    {
        $multa = Multa::findOrFail($id);
        $multa->vista = true;
        $multa->save();
        return response()->json(['message' => 'Multa marcada como vista']);
    }

    public function multaRecientePorHuesped($id)
    {
        $multa = Multa::where('huesped_id', $id)->orderBy('fecha_emision', 'desc')->first();
        return response()->json($multa);
    }
}