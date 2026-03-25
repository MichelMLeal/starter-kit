<?php

use App\Domain\Auth\Actions\RefreshTokenAction;
use App\Domain\Auth\Exceptions\TokenExpiredException;
use App\Domain\Auth\Models\RefreshToken;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('refreshes tokens for valid non-expired token', function () {
    $user = UserFactory::new()->create();

    $plainToken = 'valid-refresh-token-for-testing';
    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addDays(7),
    ]);

    $action = app(RefreshTokenAction::class);
    $result = $action->execute($plainToken);

    expect($result)->toHaveKeys(['access_token', 'refresh_token'])
        ->and($result['access_token'])->toBeString()->not->toBeEmpty()
        ->and($result['refresh_token'])->toBeString()->not->toBeEmpty();
});

it('throws exception for expired refresh token', function () {
    $user = UserFactory::new()->create();

    $plainToken = 'expired-refresh-token';
    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->subDay(),
    ]);

    $action = app(RefreshTokenAction::class);
    $action->execute($plainToken);
})->throws(TokenExpiredException::class);

it('throws exception for non-existent token', function () {
    $action = app(RefreshTokenAction::class);
    $action->execute('totally-invalid-token');
})->throws(TokenExpiredException::class);

it('throws exception when user has been deleted', function () {
    $user = UserFactory::new()->create();

    $plainToken = 'orphaned-refresh-token';
    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addDays(7),
    ]);

    $user->delete();

    $action = app(RefreshTokenAction::class);
    $action->execute($plainToken);
})->throws(TokenExpiredException::class);

it('revokes old tokens and creates new ones', function () {
    $user = UserFactory::new()->create();

    $plainToken = 'old-refresh-token';
    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addDays(7),
    ]);

    $action = app(RefreshTokenAction::class);
    $result = $action->execute($plainToken);

    // Old token should be revoked
    expect(RefreshToken::where('token', hash('sha256', $plainToken))->exists())->toBeFalse();

    // New token should exist
    expect(RefreshToken::where('user_id', $user->id)->count())->toBe(1);

    // New refresh token should be different
    expect($result['refresh_token'])->not->toBe($plainToken);
});
