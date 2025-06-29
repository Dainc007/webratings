<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ShortcodeCondition extends Model
{
    public function shortcode(): BelongsTo
    {
        return $this->belongsTo(Shortcode::class);
    }
}
