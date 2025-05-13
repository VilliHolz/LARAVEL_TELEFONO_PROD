<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'paid_with',
        'user_id',
        'contact_id',
        'status',
        'date',
        'branch_id',
    ];

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
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Relación con el modelo Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
