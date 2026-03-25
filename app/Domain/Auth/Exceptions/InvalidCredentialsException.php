<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class InvalidCredentialsException extends DomainException
{
    protected $message = 'Invalid credentials.';
}
