<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'phone',
        'address',
        'email',
        'status',
        'branch_id',
    ];

    /**
     * Relación con el modelo Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
