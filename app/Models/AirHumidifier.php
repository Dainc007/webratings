<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class AirHumidifier extends Model
{
    protected $casts = [
        'colors' => 'array',
        'mobile_features' => 'array',
        'gallery' => 'array',
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
