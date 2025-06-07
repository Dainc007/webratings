<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AirPurifier;

final class AirPurifierController extends Controller
{
    public function index()
    {
        return AirPurifier::paginate();
    }
}
