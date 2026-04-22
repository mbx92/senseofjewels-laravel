<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'client_name',
        'project_url',
        'description',
        'image_path',
        'gallery',
        'completed_at',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'completed_at' => 'date',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
