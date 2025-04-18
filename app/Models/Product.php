<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Product extends Model
{
    protected $fillable = [
        'status',
        'brand_name',
        'model',
        'price',
    ];
}
