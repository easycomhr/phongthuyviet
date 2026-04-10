<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VietnameseNameDictionary extends Model
{
    protected $table = 'vietnamese_names_dictionary';

    protected $fillable = [
        'name',
        'element',
        'gender',
        'meaning',
    ];
}

