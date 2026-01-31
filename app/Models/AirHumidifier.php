<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class AirHumidifier extends Model
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
        'mobile_features' => 'array',
        'gallery' => 'array',
        'control_other' => 'array',
        // Note: productFunctions is a MorphToMany relationship, not a JSON column cast
        // The JSON column in the database is legacy - the relationship is used instead
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
