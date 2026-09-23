<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = [
        'product_id',
        'nombre',
        'valor',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
