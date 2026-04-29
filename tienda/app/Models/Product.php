<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'tienda_id', 'nombre', 'slug', 'descripcion',
        'precio', 'precio_original', 'stock', 'imagen',
        'envio_gratis', 'cuotas', 'rating', 'rating_count', 'activo', 'estado',
    ];

    const ESTADOS = ['nuevo' => 'Nuevo', 'usado' => 'Usado', 'reacondicionado' => 'Reacondicionado'];

    protected $casts = [
        'envio_gratis' => 'boolean',
        'activo'       => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('orden');
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConTag($query, string $slug)
    {
        return $query->whereHas('tags', fn($q) => $q->where('slug', $slug));
    }

    public function getPorcentajeDescuentoAttribute(): ?int
    {
        if (! $this->precio_original || $this->precio_original <= $this->precio) {
            return null;
        }

        return (int) round((1 - $this->precio / $this->precio_original) * 100);
    }
}
