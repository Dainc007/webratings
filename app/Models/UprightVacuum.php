<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UprightVacuum extends Model
{
    protected $casts = [
        'colors' => 'array',
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'vacuum_cleaner_type' => 'array',
        'power_supply' => 'array',
        'display_type' => 'array',
        'additional_equipment' => 'array',
        'charging_station' => 'array',
    ];
}
