<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'imei',
        'name',
        'purchase_price',
        'sale_price',
        'min_stock',
        'stock',
        'brand_id',
        'category_id',
        'branch_id',
        'image',
        'status',
    ];

    /**
     * Relación con el modelo Brand.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Relación con el modelo Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con el modelo Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
