<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class LabelOverride extends Model
{
    protected $fillable = [
        'table_name',
        'element_type',
        'element_key',
        'display_label',
    ];
}
