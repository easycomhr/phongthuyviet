<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    public const CREATED_AT = null;

    protected $fillable = [
        'date',
        'total_views',
        'unique_visitors',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
