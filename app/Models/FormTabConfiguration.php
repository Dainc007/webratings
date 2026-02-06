<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class FormTabConfiguration extends Model
{
    protected $fillable = [
        'table_name',
        'tab_key',
        'tab_label',
        'sort_order',
        'is_visible',
        'columns',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_visible' => 'boolean',
        'columns' => 'integer',
    ];
}
