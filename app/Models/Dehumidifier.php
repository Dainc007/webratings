<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dehumidifier extends Model
{
    protected $casts = [
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'mobile_features' => 'array',
        'functions' => 'array',
        'modes_of_operation' => 'array',
        'functions_and_equipment_dehumi' => 'array',
        'gallery' => 'array',
        'colors' => 'array',
        'date_created' => 'datetime',
        'date_updated' => 'datetime',
        'higrostat' => 'boolean',
        'mesh_filter' => 'boolean',
        'hepa_filter' => 'boolean',
        'carbon_filter' => 'boolean',
        'ionization' => 'boolean',
        'uvc' => 'boolean',
        'uv_light_generator' => 'boolean',
        'mobile_app' => 'boolean',
        'ranking_hidden' => 'boolean',
        'main_ranking' => 'boolean',
        'is_promo' => 'boolean',
        'price' => 'decimal:2',
        'price_before' => 'decimal:2',
        'profitability_points' => 'decimal:2',
    ];
}
