<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class TokenExpiredException extends DomainException
{
    protected $message = 'Refresh token has expired.';
}
