<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Multa extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'multas';

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
