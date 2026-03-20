<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class SearchAirPurifierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'string', 'regex:/^\d+(,\d+)*$/'],
            'max' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'all' => ['nullable', 'boolean'],
            'display' => ['nullable', 'string', 'max:500'],
            'paginate' => ['nullable', 'boolean'],
        ];
    }
}
