<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityBlockedTerm extends Model
{
    protected $fillable = [
        'term',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
