<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'tienda_id', 'nombre', 'slug', 'sku', 'descripcion', 'descripcion_corta',
        'precio', 'precio_oferta', 'stock', 'imagen',
        'envio_gratis', 'retiro_en_domicilio', 'delivery', 'envio_courier', 'costo_envio', 'tiempo_entrega',
        'rating', 'rating_count', 'activo', 'estado_id', 'estado_publicacion_id', 'estado_revision_id', 'motivo_rechazo',
        'bloqueado', 'motivo_bloqueo',
        'fecha_publicacion', 'visitas', 'destacado',
    ];

    public const ESTADO_NUEVO = 1;
    public const ESTADO_USADO = 2;
    public const ESTADO_REACONDICIONADO = 3;

    public const ESTADOS = [
        self::ESTADO_NUEVO => 'Nuevo',
        self::ESTADO_USADO => 'Usado',
        self::ESTADO_REACONDICIONADO => 'Reacondicionado',
    ];

    public const ESTADO_SLUGS = [
        self::ESTADO_NUEVO => 'nuevo',
        self::ESTADO_USADO => 'usado',
        self::ESTADO_REACONDICIONADO => 'reacondicionado',
    ];

    public const PUBLICACION_ACTIVO = 1;
    public const PUBLICACION_PAUSADO = 2;
    public const PUBLICACION_VENDIDO = 3;

    public const ESTADOS_PUBLICACION = [
        self::PUBLICACION_ACTIVO => 'Activo',
        self::PUBLICACION_PAUSADO => 'Pausado',
        self::PUBLICACION_VENDIDO => 'Vendido',
    ];

    public const ESTADOS_PUBLICACION_SLUGS = [
        self::PUBLICACION_ACTIVO => 'activo',
        self::PUBLICACION_PAUSADO => 'pausado',
        self::PUBLICACION_VENDIDO => 'vendido',
    ];

    public const REVISION_PENDIENTE = 1;
    public const REVISION_APROBADO = 2;
    public const REVISION_RECHAZADO = 3;
    public const REVISION_EN_REVISION = 4;

    public const ESTADOS_REVISION = [
        self::REVISION_PENDIENTE => 'Pendiente',
        self::REVISION_APROBADO => 'Aprobado',
        self::REVISION_RECHAZADO => 'Rechazado',
        self::REVISION_EN_REVISION => 'En revisión por admin',
    ];

    public const ESTADOS_REVISION_SLUGS = [
        self::REVISION_PENDIENTE => 'pendiente',
        self::REVISION_APROBADO => 'aprobado',
        self::REVISION_RECHAZADO => 'rechazado',
        self::REVISION_EN_REVISION => 'en-revision',
    ];

    protected $casts = [
        'envio_gratis' => 'boolean',
        'retiro_en_domicilio' => 'boolean',
        'delivery' => 'boolean',
        'envio_courier' => 'boolean',
        'costo_envio' => 'integer',
        'activo'       => 'boolean',
        'precio'       => 'integer',
        'precio_oferta' => 'integer',
        'estado_id'    => 'integer',
        'estado_publicacion_id' => 'integer',
        'estado_revision_id' => 'integer',
        'bloqueado' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'visitas' => 'integer',
        'destacado' => 'boolean',
    ];

    public function getEstadoLabelAttribute(): ?string
    {
        if ($this->relationLoaded('productCondition') && $this->productCondition) {
            return $this->productCondition->nombre;
        }

        return self::ESTADOS[$this->estado_id] ?? null;
    }

    public function getEstadoSlugAttribute(): ?string
    {
        if ($this->relationLoaded('productCondition') && $this->productCondition) {
            return $this->productCondition->slug;
        }

        return self::ESTADO_SLUGS[$this->estado_id] ?? null;
    }

    public function getEstadoPublicacionLabelAttribute(): ?string
    {
        return self::ESTADOS_PUBLICACION[$this->estado_publicacion_id] ?? null;
    }

    public function getEstadoPublicacionSlugAttribute(): ?string
    {
        return self::ESTADOS_PUBLICACION_SLUGS[$this->estado_publicacion_id] ?? null;
    }

    public function getEstadoRevisionLabelAttribute(): ?string
    {
        return self::ESTADOS_REVISION[$this->estado_revision_id] ?? null;
    }

    public function getEstadoRevisionSlugAttribute(): ?string
    {
        return self::ESTADOS_REVISION_SLUGS[$this->estado_revision_id] ?? null;
    }

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

    public function deliveryTypes()
    {
        return $this->belongsToMany(DeliveryType::class)->withTimestamps()->orderBy('delivery_types.orden');
    }

    public function productCondition()
    {
        return $this->belongsTo(ProductCondition::class, 'estado_id');
    }

    public function getDeliveryTypeLabelsAttribute()
    {
        if ($this->relationLoaded('deliveryTypes') && $this->deliveryTypes->isNotEmpty()) {
            return $this->deliveryTypes->pluck('nombre');
        }

        return collect([
            $this->retiro_en_domicilio ? 'Retiro en domicilio' : null,
            $this->delivery ? 'Delivery propio' : null,
            $this->envio_courier ? 'Envio por courier' : null,
        ])->filter()->values();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class)->orderBy('orden');
    }

    public function scopeActivos($query)
    {
        return $query
            ->where('activo', true)
            ->where('estado_publicacion_id', self::PUBLICACION_ACTIVO)
            ->where('estado_revision_id', self::REVISION_APROBADO)
            ->where('bloqueado', false);
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
