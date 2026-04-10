<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrayerCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function prayers(): HasMany
    {
        return $this->hasMany(Prayer::class, 'category_id');
    }
}
