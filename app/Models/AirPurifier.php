<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AirPurifier extends Model
{
    use HasFactory;

    protected $casts = [
        'colors' => 'array',
        'functions' => 'array',
        'functions_and_equipment' => 'array',
        'certificates' => 'array',
    ];
}
