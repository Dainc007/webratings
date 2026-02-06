<?php

declare(strict_types=1);

namespace App\Filament\Components;

use Filament\Schemas\Components\Component;

class FormFieldSearch extends Component
{
    protected string $view = 'filament.components.form-field-search';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();
    }
}
