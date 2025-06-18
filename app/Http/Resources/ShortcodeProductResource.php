<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortcodeProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = $this->getTable();
        return [
            'id' => $this->id,
            'type' => $type,
            'model' => $this->model ?? null,
            'brand_name' => $this->brand_name ?? null,
            'price' => $this->price ?? null,
            'attributes' => $this->attributesToArray(),
        ];
    }
}
