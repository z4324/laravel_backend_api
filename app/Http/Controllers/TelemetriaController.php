<?php

namespace App\Http\Controllers;

use App\Models\Telemetria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelemetriaController extends Controller
{
    /**
     * Recibir y almacenar datos de telemetría desde Wear OS
     */
    public function store(Request $request)
    {
        try {
            // Validar datos recibidos
            $validator = Validator::make($request->all(), [
                'trabajador_id' => 'required|string|max:50',
                'datos' => 'required|array|min:1',
                'datos.*.hr' => 'required|integer|min:0',
                'datos.*.baro' => 'required|numeric',
                'datos.*.accX' => 'required|numeric',
                'datos.*.accY' => 'required|numeric',
                'datos.*.accZ' => 'required|numeric',
                'datos.*.timestamp' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $trabajadorId = $request->trabajador_id;
            $datos = $request->datos;
            $insertados = 0;

            // Usar transacción para asegurar integridad
            DB::beginTransaction();

            foreach ($datos as $dato) {
                Telemetria::create([
                    'trabajador_id' => $trabajadorId,
                    'hr' => $dato['hr'],
                    'baro' => $dato['baro'],
                    'accX' => $dato['accX'],
                    'accY' => $dato['accY'],
                    'accZ' => $dato['accZ'],
                    'timestamp' => $dato['timestamp']
                ]);
                $insertados++;
            }

            DB::commit();

            Log::info("Telemetría recibida", [
                'trabajador_id' => $trabajadorId,
                'registros' => $insertados
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos de telemetría recibidos correctamente',
                'registros_insertados' => $insertados,
                'trabajador_id' => $trabajadorId
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Error al guardar telemetría", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los datos de telemetría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener telemetría de un trabajador
     */
    public function index(Request $request)
    {
        try {
            $trabajadorId = $request->query('trabajador_id');
            $limit = $request->query('limit', 100);

            $query = Telemetria::orderBy('timestamp', 'desc');

            if ($trabajadorId) {
                $query->where('trabajador_id', $trabajadorId);
            }

            $telemetrias = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $telemetrias,
                'total' => $telemetrias->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener telemetría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de telemetría
     */
    public function estadisticas(Request $request)
    {
        try {
            $trabajadorId = $request->query('trabajador_id');

            $query = Telemetria::query();

            if ($trabajadorId) {
                $query->where('trabajador_id', $trabajadorId);
            }

            $estadisticas = [
                'total_registros' => $query->count(),
                'hr_promedio' => round($query->avg('hr'), 2),
                'hr_max' => $query->max('hr'),
                'hr_min' => $query->min('hr'),
                'ultimo_registro' => $query->latest('timestamp')->first(),
                'primer_registro' => $query->oldest('timestamp')->first()
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
