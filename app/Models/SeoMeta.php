<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'path', 'title', 'description', 'keywords', 'canonical_url', 'index'
    ];

    protected $casts = [
        'keywords' => 'array',
        'index' => 'boolean',
    ];
}
