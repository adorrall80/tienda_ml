<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCondition extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'estado_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
