<?php

declare(strict_types=1);

it('returns a successful response', function (): void {
    $response = $this->get('/');

    // The root route redirects to the Filament admin panel
    $response->assertStatus(302);
    $response->assertRedirect(route('filament.admin.resources.air-purifiers.index'));
});
