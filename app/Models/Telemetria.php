<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telemetria extends Model
{
    protected $table = 'telemetrias';

    protected $fillable = [
        'trabajador_id',
        'hr',
        'baro',
        'accX',
        'accY',
        'accZ',
        'timestamp'
    ];

    protected $casts = [
        'hr' => 'integer',
        'baro' => 'float',
        'accX' => 'float',
        'accY' => 'float',
        'accZ' => 'float',
        'timestamp' => 'datetime'
    ];
}
