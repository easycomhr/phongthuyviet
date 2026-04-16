<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public const TYPES = [
        'bug' => 'Báo lỗi',
        'feature' => 'Đề xuất tính năng',
        'compliment' => 'Khen ngợi',
        'other' => 'Khác',
    ];

    protected $table = 'feedbacks';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'type',
        'content',
        'ip_hash',
    ];
}
