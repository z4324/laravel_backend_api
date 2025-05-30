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
            'monto' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:pendiente,pagada',
            'huesped_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $multa = Multa::create([
            'monto' => $request->monto,
            'motivo' => $request->motivo,
            'fecha_emision' => $request->fecha_emision,
            'estado' => $request->estado,
            'huesped_id' => $request->huesped_id,
            'fecha_notificacion' => now(),
        ]);

        return response()->json(['ok' => true, 'multa' => $multa]);
    }

    public function multasPorHuesped($id)
    {
        $multas = Multa::where('huesped_id', $id)
            ->orderBy('fecha_notificacion', 'desc')
            ->get();

        $result = $multas->map(function($m){
            return [
                '_id' => (string)$m->_id,
                'monto' => $m->monto,
                'motivo' => $m->motivo,
                'fecha_emision' => $m->fecha_emision,
                'estado' => $m->estado,
                'fecha_notificacion' => $m->fecha_notificacion,
            ];
        });
        
        return response()->json($result);
    }

    public function multaRecientePorHuesped($id)
    {
        $multa = Multa::where('huesped_id', $id)
            ->orderBy('fecha_notificacion', 'desc')
            ->first();

        if (!$multa) {
            return response()->json(null);
        }

        return response()->json([
            '_id' => (string)$multa->_id,
            'monto' => $multa->monto,
            'motivo' => $multa->motivo,
            'fecha_emision' => $multa->fecha_emision,
            'estado' => $multa->estado,
            'fecha_notificacion' => $multa->fecha_notificacion,
        ]);
    }
}