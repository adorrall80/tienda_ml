<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'tienda_id',
        'producto_nombre',
        'producto_slug',
        'tienda_nombre',
        'cantidad',
        'precio_unitario',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }
}
