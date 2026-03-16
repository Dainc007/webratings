<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class FormLayoutItem extends Model
{
    protected $fillable = [
        'table_name',
        'element_type',
        'element_key',
        'parent_key',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
