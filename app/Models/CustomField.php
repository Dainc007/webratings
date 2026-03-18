<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomFieldStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class CustomField extends Model
{
    protected $table = 'custom_fields';

    protected $fillable = [
        'table_name',
        'column_name',
        'column_type',
        'display_name',
        'status',
        'error_message',
        'migration_file',
    ];

    protected $casts = [
        'status' => CustomFieldStatus::class,
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CustomFieldStatus::ACTIVE);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [CustomFieldStatus::PENDING, CustomFieldStatus::DELETING]);
    }
}
