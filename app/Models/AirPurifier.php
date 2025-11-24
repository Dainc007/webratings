<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\IonizerType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AirPurifier extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => Status::class,
        'colors' => 'array',
        'functions' => 'array',
        'functions_and_equipment' => 'array',
        'certificates' => 'array',
        'partner_link_rel_2' => 'array',
        'ceneo_link_rel_2' => 'array',
        'ionizer_type' => IonizerType::class,
    ];
}
