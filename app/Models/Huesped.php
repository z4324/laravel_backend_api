<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Huesped extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'huespedes';

    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono',
        'correo',
        'contrasena',
        'fecha_registro',
    ];
}