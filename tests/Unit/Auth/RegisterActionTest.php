<?php

use App\Domain\Auth\Actions\RegisterAction;
use App\Domain\Auth\DTOs\RegisterDTO;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a user with correct data', function () {
    $action = app(RegisterAction::class);

    $dto = new RegisterDTO(
        name: 'Jane Doe',
        email: 'jane@example.com',
        password: 'securepassword',
    );

    $user = $action->execute($dto);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Jane Doe')
        ->and($user->email)->toBe('jane@example.com');

    $this->assertDatabaseHas('users', [
        'email' => 'jane@example.com',
        'name' => 'Jane Doe',
    ]);
});

it('hashes the password', function () {
    $action = app(RegisterAction::class);

    $dto = new RegisterDTO(
        name: 'Jane Doe',
        email: 'jane@example.com',
        password: 'securepassword',
    );

    $user = $action->execute($dto);

    expect($user->password)->not->toBe('securepassword')
        ->and(Hash::check('securepassword', $user->password))->toBeTrue();
});
