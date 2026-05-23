<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'tienda_id', 'nombre', 'slug', 'sku', 'descripcion', 'descripcion_corta',
        'precio', 'precio_oferta', 'stock', 'imagen',
        'envio_gratis', 'rating', 'rating_count', 'activo', 'estado',
    ];

    const ESTADOS = ['nuevo' => 'Nuevo', 'usado' => 'Usado', 'reacondicionado' => 'Reacondicionado'];

    protected $casts = [
        'envio_gratis' => 'boolean',
        'activo'       => 'boolean',
        'precio'       => 'integer',
        'precio_oferta' => 'integer',
    ];

    public function getPrecioFinalAttribute(): ?int
    {
        if ($this->precio_oferta !== null && $this->precio_oferta >= 0) {
            return (int) $this->precio_oferta;
        }

        return $this->precio !== null ? (int) $this->precio : null;
    }

    public function getPrecioReferenciaAttribute(): ?int
    {
        if ($this->precio_oferta !== null && $this->precio !== null && $this->precio_oferta < $this->precio) {
            return (int) $this->precio;
        }

        return null;
    }

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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePublicados($query)
    {
        return $query
            ->activos()
            ->whereHas('tienda', fn($q) => $q->where('activa', true));
    }

    public function scopeConTag($query, string $slug)
    {
        return $query->whereHas('tags', fn($q) => $q->where('slug', $slug));
    }

    public function getPorcentajeDescuentoAttribute(): ?int
    {
        $referencia = $this->precio_referencia;
        $final = $this->precio_final;

        if (! $referencia || $final === null || $referencia <= $final) {
            return null;
        }

        return (int) round((1 - $final / $referencia) * 100);
    }
}
