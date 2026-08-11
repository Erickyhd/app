<?php

namespace App\Modules\Users\Application\CreateUser;

class CreateUserCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password
    ) {}
}
