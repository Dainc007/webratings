<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class CustomField extends Model
{
    protected $table = 'custom_fields';

    protected $fillable = [
        'table_name',
        'column_name',
        'column_type',
        'display_name',
    ];
}
