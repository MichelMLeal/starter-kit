<?php

use App\Infrastructure\Auth\Providers\AuthDomainServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AuthDomainServiceProvider::class,
];
