<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Multa extends Model
{
    protected $table = 'multas';

    protected $fillable = [
        'monto',
        'motivo',
        'fecha_emision',
        'estado',
        'huesped_id',
        'fecha_notificacion',
        'vista',
    ];
}
