<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'brand_id',
        'category_id',
        'sku_base',
        'status',
        'is_featured',
        'is_active',
        'average_rating',
        'review_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function description()
    {
        return $this->hasOne(ProductDescription::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function skus()
    {
        return $this->hasMany(Sku::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    public function descriptions()
    {
        return $this->hasMany(ProductDescription::class);
    }
}