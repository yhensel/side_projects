<?php

declare(strict_types=1);

namespace App\Application\Group\Command;

final class JoinGroupCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $invitationCode,
    ) {
    }
}
