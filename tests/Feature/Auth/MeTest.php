<?php

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns authenticated user', function () {
    $user = UserFactory::new()->create([
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
    ])->getJson('/api/auth/me');

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['id', 'name', 'email']])
        ->assertJsonPath('data.email', $user->email)
        ->assertJsonPath('data.name', $user->name);
});

it('fails without auth', function () {
    $response = $this->getJson('/api/auth/me');

    $response->assertStatus(401);
});
