<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

class RegisterUserResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $firstName,
    ) {
    }
}
