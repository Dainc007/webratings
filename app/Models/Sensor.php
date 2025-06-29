<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Sensor extends Model
{
    protected $casts = [
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'mobile_features' => 'array',
    ];
}
