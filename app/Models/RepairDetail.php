<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'quantity',
        'repair_id',
        'product_id',
    ];

    /**
     * Relación con el modelo Repair.
     */
    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    /**
     * Relación con el modelo Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
