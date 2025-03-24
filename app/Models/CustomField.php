<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomField extends Model
{
    protected $table = 'custom_fields';

    protected $fillable = [
        'table_name',
        'column_name',
        'column_type',
        'display_name'
    ];
}
