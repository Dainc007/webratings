<?php

declare(strict_types=1);

namespace App\Filament\Pages;

final class Login extends \Filament\Pages\Auth\Login
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ]);
    }
}
