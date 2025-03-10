<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

final class ProductController extends Controller
{
    public function index()
    {
        return Product::orderBy('price', 'desc')->get();
    }
}
