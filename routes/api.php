<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AirHumidifierController;
use App\Http\Controllers\Api\AirPurifierController;
use App\Http\Controllers\Api\ShortcodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResources([
    'airPurifiers' => AirPurifierController::class,
    'airHumidifiers' => AirHumidifierController::class,
    'shortcodes' => ShortcodeController::class,
]);
