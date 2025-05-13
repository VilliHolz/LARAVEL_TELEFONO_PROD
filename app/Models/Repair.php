<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'imei',
        'entry_date',
        'promised_date',
        'observations',
        'advance',
        'key',
        'pin',
        'total',
        'status',
        'contact_id',
        'cash_register_id',
        'brand_id',
        'user_id',
        'branch_id',
    ];

    /**
     * Relación con el modelo Contact.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Relación con el modelo Brand.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

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

    public function details()
    {
        return $this->hasMany(RepairDetail::class);
    }
}
