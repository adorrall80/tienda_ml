<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReport extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'report_reason_id',
        'comentarios',
        'estado'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reason()
    {
        return $this->belongsTo(ReportReason::class, 'report_reason_id');
    }
}
