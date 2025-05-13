<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'quantity',
        'quote_id',
        'product_id',
    ];

    /**
     * Relación con el modelo Quote.
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Relación con el modelo Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
