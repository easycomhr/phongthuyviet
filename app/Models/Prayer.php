<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prayer extends Model
{
    protected $fillable = ['category_id', 'title', 'slug', 'content'];

    public function prayerCategory(): BelongsTo
    {
        return $this->belongsTo(PrayerCategory::class, 'category_id');
    }
}
