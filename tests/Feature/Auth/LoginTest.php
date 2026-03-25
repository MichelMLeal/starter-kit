<?php

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in successfully', function () {
    UserFactory::new()->create([
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'access_token',
                'refresh_token',
                'token_type',
            ],
        ]);
});

it('fails with wrong password', function () {
    UserFactory::new()->create([
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

it('fails with non-existent email', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'nobody@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(401);
});

it('fails with missing fields', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});
