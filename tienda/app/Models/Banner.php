<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'badge', 'titulo', 'subtitulo', 'precio',
        'imagen', 'url', 'btn_texto', 'orden', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }
}
