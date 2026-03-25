<?php

use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\Models\RefreshToken;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes current access token', function () {
    $user = UserFactory::new()->create();
    $token = $user->createToken('auth-token');

    expect($user->tokens()->count())->toBe(1);

    $user->withAccessToken($token->accessToken);

    $action = app(LogoutAction::class);
    $action->execute($user);

    expect($user->tokens()->count())->toBe(0);
});

it('revokes all refresh tokens for user', function () {
    $user = UserFactory::new()->create();

    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', 'token-1'),
        'expires_at' => now()->addDays(7),
    ]);

    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', 'token-2'),
        'expires_at' => now()->addDays(7),
    ]);

    expect(RefreshToken::where('user_id', $user->id)->count())->toBe(2);

    $user->withAccessToken($user->createToken('auth-token')->accessToken);

    $action = app(LogoutAction::class);
    $action->execute($user);

    expect(RefreshToken::where('user_id', $user->id)->count())->toBe(0);
});
