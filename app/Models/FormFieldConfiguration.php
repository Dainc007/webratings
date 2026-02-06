<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class FormFieldConfiguration extends Model
{
    protected $fillable = [
        'table_name',
        'field_name',
        'tab_key',
        'section_key',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_visible' => 'boolean',
    ];
}
