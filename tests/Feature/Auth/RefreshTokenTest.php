<?php

use App\Domain\Auth\Models\RefreshToken;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('refreshes tokens successfully', function () {
    UserFactory::new()->create([
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $loginResponse = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $refreshToken = $loginResponse->json('data.refresh_token');

    $response = $this->postJson('/api/auth/refresh', [
        'refresh_token' => $refreshToken,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'refresh_token',
                'token_type',
            ],
        ]);

    expect($response->json('data.refresh_token'))->not->toBe($refreshToken);
});

it('fails with invalid refresh token', function () {
    $response = $this->postJson('/api/auth/refresh', [
        'refresh_token' => 'invalid-random-token-string',
    ]);

    $response->assertStatus(401);
});

it('fails with expired refresh token', function () {
    $user = UserFactory::new()->create();

    $plainToken = 'expired-token-string-for-testing-purposes';

    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->subDay(),
    ]);

    $response = $this->postJson('/api/auth/refresh', [
        'refresh_token' => $plainToken,
    ]);

    $response->assertStatus(401);
});

it('fails when user has been deleted', function () {
    $user = UserFactory::new()->create();

    $plainToken = 'orphaned-token-for-deleted-user';

    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addDays(7),
    ]);

    $user->delete();

    $response = $this->postJson('/api/auth/refresh', [
        'refresh_token' => $plainToken,
    ]);

    $response->assertStatus(401);
});
