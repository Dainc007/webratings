<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class FormSectionConfiguration extends Model
{
    protected $fillable = [
        'table_name',
        'tab_key',
        'section_key',
        'section_label',
        'sort_order',
        'columns',
        'is_collapsible',
        'is_visible',
        'depends_on',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'columns' => 'integer',
        'is_collapsible' => 'boolean',
        'is_visible' => 'boolean',
    ];
}
