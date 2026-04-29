<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['pagina', 'modulo', 'titulo', 'config', 'orden', 'activo'];

    protected $casts = [
        'config' => 'array',
        'activo' => 'boolean',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public function scopeDePagina($query, string $pagina)
    {
        return $query->where('pagina', $pagina);
    }
}
