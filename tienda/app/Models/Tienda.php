<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'slug',
        'descripcion',
        'contacto_email',
        'contacto_telefono',
        'contacto_whatsapp',
        'contacto_direccion',
        'logo',
        'activa',
    ];

    protected $casts = ['activa' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productos()
    {
        return $this->hasMany(Product::class, 'tienda_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'tienda_id');
    }
}
