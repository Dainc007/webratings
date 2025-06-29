<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Shortcode extends Model
{
    protected $casts = [
        'product_types' => 'array',
    ];

    public function conditions(): HasMany
    {
        return $this->hasMany(ShortcodeCondition::class);
    }
}
