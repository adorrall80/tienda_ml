<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['nombre', 'slug', 'label', 'color'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
