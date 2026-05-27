<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = [
        'slug',
        'nombre',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'estado', 'slug');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
