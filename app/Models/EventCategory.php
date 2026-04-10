<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_popular'];

    protected $attributes = [
        'is_popular' => false,
    ];

    protected $casts = [
        'is_popular' => 'boolean',
    ];

    public function scopePopular(Builder $query): Builder
    {
        return $query->where('is_popular', true);
    }
}
