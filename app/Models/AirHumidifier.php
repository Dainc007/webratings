<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AirHumidifier extends Model
{
    protected $casts = [
        'colors' => 'array',
        'functions' => 'array',
        'mobile_features' => 'array',
        'gallery' => 'array',
    ];
}
