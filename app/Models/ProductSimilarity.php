<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSimilarity extends Model
{
    protected $fillable = [
        'product_id',
        'similar_product_id',
        'similarity_score',
        'similarity_type',
        'computed_at',
    ];

    protected $casts = [
        'similarity_score' => 'decimal:4',
        'computed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function similarProduct()
    {
        return $this->belongsTo(Product::class, 'similar_product_id');
    }
}
