<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSale extends Model
{
    use HasFactory;
    protected $table = 'payments_sales';
    protected $fillable = [
        'amount',
        'date',
        'credit_id',
        'cash_register_id',
        'user_id'
    ];

    /**
     * Relación con el modelo CreditSale.
     */
    public function creditSale()
    {
        return $this->belongsTo(CreditSale::class, 'credit_id');
    }

    /**
     * Relación con el modelo CashRegister.
     */
    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
