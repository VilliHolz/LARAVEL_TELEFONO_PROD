<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'date',
        'status',
        'sale_id',
    ];

    /**
     * Relación con el modelo Sale.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentSale::class, 'credit_id');
    }
}
