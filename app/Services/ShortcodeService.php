<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Product;
use App\Models\Shortcode;
use Illuminate\Database\Eloquent\Builder;

final class ShortcodeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findShortcodeByNameOrId(Shortcode $identifier): Shortcode
    {
        return Shortcode::where('name', $identifier)
            ->orWhere('id', $identifier)
            ->with('conditions')
            ->firstOrFail();
    }

    public function executeShortcode(Shortcode $shortcode)
    {
        $results = collect();
        $conditions = $shortcode->conditions;
        $productTypes = $shortcode->product_types;

        foreach ($productTypes as $type) {
            $query = Product::getQueryForType($type);

            if (! $query instanceof Builder) {
                continue;
            }

            $this->applyConditionsToQuery($query, $conditions);
            $results = $results->merge($query->get());
        }

        return $results;
    }

    private function applyConditionsToQuery(Builder $query, $conditions): void
    {
        foreach ($conditions as $condition) {
            $field = $condition->field;
            $operator = $condition->operator;
            $value = $this->castValue($condition->value, $condition->type);

            $query->where($field, $operator, $value);
        }
    }

    private function castValue(string $value, ?string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $value,
        };
    }
}
