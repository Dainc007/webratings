<?php

namespace App\Models;

use Database\Factories\ProductTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    /** @use HasFactory<ProductTypeFactory> */
    use HasFactory;

    protected $fillable = ['name'];
}
