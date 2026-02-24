<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
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
        'status' => Status::class,
        'colors' => 'array',
        'mobile_features' => 'array',
        'gallery' => 'array',
        'control_other' => 'array',
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
