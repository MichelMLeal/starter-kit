<?php

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('throws exception for invalid credentials', function () {
    UserFactory::new()->create([
        'email' => 'john@example.com',
        'password' => 'correctpassword',
    ]);

    $action = app(LoginAction::class);

    $dto = new LoginDTO(
        email: 'john@example.com',
        password: 'wrongpassword',
    );

    $action->execute($dto);
})->throws(InvalidCredentialsException::class);
