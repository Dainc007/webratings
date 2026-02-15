<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class Dehumidifier extends Model
{
    /**
     * Allow all attributes to be mass assignable.
     * Filament handles permission/validation.
     *
     * @var array<string>
     */
    protected $guarded = [];

    protected $casts = [
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'mobile_features' => 'array',
        'modes_of_operation' => 'array',
        'functions_and_equipment_dehumi' => 'array',
        'gallery' => 'array',
        'colors' => 'array',
        'date_created' => 'datetime',
        'date_updated' => 'datetime',
        'higrostat' => 'boolean', // Changed from array to boolean: form now uses Toggle
        'mesh_filter' => 'boolean',
        'hepa_filter' => 'boolean',
        'carbon_filter' => 'boolean',
        'ionization' => 'boolean',
        'uvc' => 'boolean',
        'uv_light_generator' => 'array', // JSON column in database
        'mobile_app' => 'boolean',
        'ranking_hidden' => 'boolean',
        'main_ranking' => 'boolean',
        'is_promo' => 'boolean',
        'price' => 'decimal:2',
        'price_before' => 'decimal:2',
        'profitability_points' => 'decimal:2',
    ];

    public function types(): MorphToMany
    {
        return $this->morphToMany(ProductType::class, 'model', 'model_has_product_types');
    }

    public function productFunctions(): MorphToMany
    {
        return $this->morphToMany(ProductFunction::class, 'model', 'model_has_product_functions');
    }
}
