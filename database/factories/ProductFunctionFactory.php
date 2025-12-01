<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ProductFunction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductFunction>
 */
final class ProductFunctionFactory extends Factory
{
    protected $model = ProductFunction::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(asText: true),
        ];
    }
}
