<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirHumidifier extends Model
{
    protected $casts = [
        'colors' => 'array',
        'functions' => 'array',
        'mobile_features' => 'array',
        'gallery' => 'array',
    ];
}
