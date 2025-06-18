<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShortcodeProductResource;
use App\Models\Shortcode;
use App\Models\AirPurifier;
use App\Models\AirHumidifier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ShortcodeController extends Controller
{
    public function show(Request $request, string $shortcode): AnonymousResourceCollection|Response
    {
        $shortcodeModel = Shortcode::where('name', $shortcode)
            ->orWhere('id', $shortcode)
            ->with('conditions')
            ->first();

        if (! $shortcodeModel) {
            return response(['message' => 'Shortcode not found'], SymfonyResponse::HTTP_NOT_FOUND);
        }

        $results = collect();
        $conditions = $shortcodeModel->conditions;
        $productTypes = $shortcodeModel->product_types;

        foreach ($productTypes as $type) {
            $query = match ($type) {
                'air_purifiers' => AirPurifier::query(),
                'air_humidifiers' => AirHumidifier::query(),
                default => null,
            };
            if (! $query) {
                continue;
            }
            foreach ($conditions as $condition) {
                $field = $condition->field;
                $operator = $condition->operator;
                $value = $condition->value;
                $typeCast = $condition->type;
                if ($typeCast === 'integer') {
                    $value = (int) $value;
                } elseif ($typeCast === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                $query->where($field, $operator, $value);
            }
            $results = $results->merge($query->get());
        }

        return ShortcodeProductResource::collection($results);
    }
}
