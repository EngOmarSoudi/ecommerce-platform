<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'business_license_number',
        'tax_id',
        'status',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->hasOne(SellerStore::class);
    }

    public function documents()
    {
        return $this->hasMany(SellerDocument::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}