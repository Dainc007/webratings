<?php

declare(strict_types=1);

use App\Models\User;

test('registration screen can be rendered', function (): void {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function (): void {
    $uniqueEmail = 'test-' . time() . '@example.com';
    
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => $uniqueEmail,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Check if user was created successfully
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => $uniqueEmail,
    ]);
    
    // Check for successful response (either 200/201 for JSON or 302 for redirect)
    $this->assertContains($response->getStatusCode(), [200, 201, 302], 
        'Registration should return success status (200/201) or redirect (302)');
});
