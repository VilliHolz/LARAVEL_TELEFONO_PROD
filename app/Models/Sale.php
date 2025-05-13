<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'date',
        'status',
        'paid_with',
        'method',
        'payment_method_id',
        'branch_id',
        'user_id',
        'contact_id',
        'cash_register_id',
    ];

    /**
     * Relación con el modelo PaymentMethod.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo Contact.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Relación con el modelo CashRegister.
     */
    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    /**
     * Relación con el modelo Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
