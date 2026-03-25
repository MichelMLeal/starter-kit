<?php

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs out successfully', function () {
    UserFactory::new()->create([
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $loginResponse = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $accessToken = $loginResponse->json('data.access_token');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$accessToken}",
    ])->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logged out successfully.']);
});

it('fails without auth token', function () {
    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(401);
});
