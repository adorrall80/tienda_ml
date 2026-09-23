<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryType extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
