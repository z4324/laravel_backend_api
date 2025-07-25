<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Huesped extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'huespedes';

    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono',
        'correo',
        'contrasena',
        'rol', 
        'fecha_registro',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}
