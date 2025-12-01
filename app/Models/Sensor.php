<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class Sensor extends Model
{
    protected $casts = [
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'mobile_features' => 'array',
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
