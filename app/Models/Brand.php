<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the user's first name.
     */
    private function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): string => ucfirst($value),
            set: fn (string $value) => mb_strtolower($value),
        );
    }
}
