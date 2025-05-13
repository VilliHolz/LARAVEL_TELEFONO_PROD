<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'start_time',
        'end_date',
        'initial_amount',
        'final_amount',
        'status',
        'user_id',
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
     * Relación con el modelo Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
