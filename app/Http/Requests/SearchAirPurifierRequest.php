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
            'max' => ['nullable'],
            'all' => ['nullable'],
            'display' => ['nullable'],
            'paginate' => ['nullable'],
        ];
    }
}
