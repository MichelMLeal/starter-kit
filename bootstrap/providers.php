<?php

use App\Infrastructure\Auth\Providers\AuthDomainServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\HorizonServiceProvider;

return [
    AuthDomainServiceProvider::class,
    AppServiceProvider::class,
    HorizonServiceProvider::class,
];
