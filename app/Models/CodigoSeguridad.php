<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CodigoSeguridad extends Model
{
    use HasFactory;

    protected $table = 'codigo_seguridads';

    protected $fillable = [
        'huesped_id',
        'codigo',
        'expires_at',
    ];

    public $timestamps = true;

    public function huesped()
    {
        return $this->belongsTo(Huesped::class, 'huesped_id');
    }
}
