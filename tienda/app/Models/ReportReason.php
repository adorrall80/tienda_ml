<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportReason extends Model
{
    protected $fillable = ['nombre', 'activo'];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
