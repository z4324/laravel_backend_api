# API de Telemetría - Wear OS Integration

## 📡 Endpoint Creado

### POST `/api/telemetria`
Recibe datos de telemetría desde dispositivos Wear OS.

**URL:** `http://tu-dominio.com/api/telemetria`

## 📋 Estructura de la Base de Datos

Tabla: `telemetrias`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID autoincremental |
| trabajador_id | string | Identificador del trabajador |
| hr | integer | Frecuencia cardíaca |
| baro | float | Presión barométrica |
| accX | float | Acelerómetro eje X |
| accY | float | Acelerómetro eje Y |
| accZ | float | Acelerómetro eje Z |
| timestamp | timestamp | Marca de tiempo del dato |
| created_at | timestamp | Fecha de creación en BD |
| updated_at | timestamp | Fecha de actualización |

## 🔧 Actualización en Android Studio (Kotlin)

### Cambiar la URL en tu código Wear OS:

```kotlin
private fun enviarDatosBackend() {
    if (ultimosDatos.isEmpty()) {
        textoUltimaSync.text = "No hay datos para enviar"
        return
    }
    
    launch(Dispatchers.IO) {
        try {
            val json = JSONObject().apply {
                put("trabajador_id", "TR001")
                put("datos", org.json.JSONArray().apply {
                    ultimosDatos.takeLast(10).forEach { dato ->
                        put(JSONObject().apply {
                            put("hr", dato.frecuenciaCardiaca)
                            put("baro", dato.presionBarometro)
                            put("accX", dato.acelerometroX)
                            put("accY", dato.acelerometroY)
                            put("accZ", dato.acelerometroZ)
                            put("timestamp", dato.timestamp)
                        })
                    }
                })
            }
            
            val mediaType = "application/json; charset=utf-8".toMediaType()
            val body = json.toString().toRequestBody(mediaType)
            
            val request = Request.Builder()
                // ⚠️ CAMBIAR ESTA URL POR TU DOMINIO O IP
                .url("http://TU_IP_O_DOMINIO/api/telemetria")
                .post(body)
                .build()
            
            okHttpClient.newCall(request).execute().use { response ->
                launch(Dispatchers.Main) {
                    if (response.isSuccessful) {
                        textoUltimaSync.text = "✅ Enviado: ${java.util.Date()}"
                        Log.d("Backend", "Datos enviados correctamente")
                    } else {
                        textoUltimaSync.text = "❌ Error: ${response.code}"
                    }
                }
            }
        } catch (e: Exception) {
            launch(Dispatchers.Main) {
                textoUltimaSync.text = "❌ Error: ${e.message}"
            }
            Log.e("Backend", "Error: ${e.message}")
        }
    }
}
```

## 📝 Ejemplo de Request

### JSON enviado desde Wear OS:
```json
{
  "trabajador_id": "TR001",
  "datos": [
    {
      "hr": 75,
      "baro": 1013.25,
      "accX": 0.15,
      "accY": -0.22,
      "accZ": 9.81,
      "timestamp": "2025-11-06T14:30:00Z"
    },
    {
      "hr": 78,
      "baro": 1013.28,
      "accX": 0.12,
      "accY": -0.18,
      "accZ": 9.79,
      "timestamp": "2025-11-06T14:30:05Z"
    }
  ]
}
```

## ✅ Respuesta Exitosa (201)
```json
{
  "success": true,
  "message": "Datos de telemetría recibidos correctamente",
  "registros_insertados": 2,
  "trabajador_id": "TR001"
}
```

## ❌ Respuesta de Error (422)
```json
{
  "success": false,
  "message": "Datos inválidos",
  "errors": {
    "datos.0.hr": ["El campo hr es requerido"]
  }
}
```

## 🔍 Endpoints Adicionales

### GET `/api/telemetria`
Obtener telemetrías almacenadas.

**Parámetros de consulta:**
- `trabajador_id` (opcional): Filtrar por trabajador
- `limit` (opcional, default: 100): Límite de registros

**Ejemplo:**
```
GET /api/telemetria?trabajador_id=TR001&limit=50
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "trabajador_id": "TR001",
      "hr": 75,
      "baro": 1013.25,
      "accX": 0.15,
      "accY": -0.22,
      "accZ": 9.81,
      "timestamp": "2025-11-06T14:30:00.000000Z",
      "created_at": "2025-11-06T14:30:15.000000Z",
      "updated_at": "2025-11-06T14:30:15.000000Z"
    }
  ],
  "total": 1
}
```

### GET `/api/telemetria/estadisticas`
Obtener estadísticas de telemetría.

**Parámetros de consulta:**
- `trabajador_id` (opcional): Filtrar por trabajador

**Ejemplo:**
```
GET /api/telemetria/estadisticas?trabajador_id=TR001
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "total_registros": 250,
    "hr_promedio": 76.5,
    "hr_max": 120,
    "hr_min": 60,
    "ultimo_registro": {
      "id": 250,
      "trabajador_id": "TR001",
      "hr": 78,
      "timestamp": "2025-11-06T15:00:00.000000Z"
    },
    "primer_registro": {
      "id": 1,
      "trabajador_id": "TR001",
      "hr": 75,
      "timestamp": "2025-11-06T14:00:00.000000Z"
    }
  }
}
```

## 🧪 Testing con cURL

### Enviar telemetría de prueba:
```bash
curl -X POST http://localhost:8000/api/telemetria \
  -H "Content-Type: application/json" \
  -d '{
    "trabajador_id": "TR001",
    "datos": [
      {
        "hr": 75,
        "baro": 1013.25,
        "accX": 0.15,
        "accY": -0.22,
        "accZ": 9.81,
        "timestamp": "2025-11-06T14:30:00Z"
      }
    ]
  }'
```

### Consultar telemetría:
```bash
curl -X GET "http://localhost:8000/api/telemetria?trabajador_id=TR001&limit=10"
```

### Obtener estadísticas:
```bash
curl -X GET "http://localhost:8000/api/telemetria/estadisticas?trabajador_id=TR001"
```

## 🔐 Seguridad (Opcional)

Si deseas proteger estos endpoints con autenticación, puedes moverlos dentro de un middleware `auth:sanctum`:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/telemetria', [TelemetriaController::class, 'store']);
    Route::get('/telemetria', [TelemetriaController::class, 'index']);
    Route::get('/telemetria/estadisticas', [TelemetriaController::class, 'estadisticas']);
});
```

## 📊 Monitoreo

Los datos se registran en los logs de Laravel:
- Ubicación: `storage/logs/laravel.log`
- Información registrada: trabajador_id, número de registros, errores

## ⚙️ Configuración de CORS

Si tienes problemas de CORS, asegúrate de configurar `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

## 📱 Compatibilidad

- ✅ Wear OS
- ✅ Android móvil
- ✅ Aplicaciones Kotlin/Java
- ✅ Cualquier cliente HTTP que soporte JSON
