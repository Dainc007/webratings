<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchAirPurifierRequest extends FormRequest
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
