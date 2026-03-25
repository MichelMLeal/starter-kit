<?php

// This model has been moved to App\Domain\Auth\Models\User
// This file exists for backwards compatibility with Laravel's default config

namespace App\Models;

use App\Domain\Auth\Models\User as DomainUser;

class User extends DomainUser {}
