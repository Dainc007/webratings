<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class UprightVacuum extends Model
{
    /**
     * Allow all attributes to be mass assignable.
     * Filament handles permission/validation.
     *
     * @var array<string>
     */
    protected $guarded = [];

    protected $casts = [
        'colors' => 'array',
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'vacuum_cleaner_type' => 'array',
        'power_supply' => 'array',
        'display_type' => 'array',
        'additional_equipment' => 'array',
        'charging_station' => 'array',
        'type_of_washing' => 'array',
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
