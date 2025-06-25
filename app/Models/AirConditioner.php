<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirConditioner extends Model
{
    protected $casts = [
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'colors' => 'array',
        'mobile_features' => 'array',
        'functions' => 'array',
        'functions_and_equipment_condi' => 'array',
        'gallery' => 'array',
    ];
}
