<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

class LoginUserResponse
{
    public function __construct(
        public readonly string $token,
        public readonly string $userId,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $biologicalSex,
        public readonly string $refreshToken,
    ) {
    }
}
