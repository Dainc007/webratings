<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductTypeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;

final class ProductType extends Model
{
    /** @use HasFactory<ProductTypeFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    // Inverse polymorphic many-to-many to all product models
    public function airPurifiers(): MorphedByMany
    {
        return $this->morphedByMany(AirPurifier::class, 'model', 'model_has_product_types');
    }

    public function airHumidifiers(): MorphedByMany
    {
        return $this->morphedByMany(AirHumidifier::class, 'model', 'model_has_product_types');
    }

    public function airConditioners(): MorphedByMany
    {
        return $this->morphedByMany(AirConditioner::class, 'model', 'model_has_product_types');
    }

    public function dehumidifiers(): MorphedByMany
    {
        return $this->morphedByMany(Dehumidifier::class, 'model', 'model_has_product_types');
    }

    public function sensors(): MorphedByMany
    {
        return $this->morphedByMany(Sensor::class, 'model', 'model_has_product_types');
    }

    public function uprightVacuums(): MorphedByMany
    {
        return $this->morphedByMany(UprightVacuum::class, 'model', 'model_has_product_types');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): string => ucfirst($value),
            set: fn (string $value) => mb_strtolower($value),
        );
    }
}
