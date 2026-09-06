<?php

declare(strict_types=1);

namespace App\Application\Activity\Command;

final class CreateActivityCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly ?string $groupId,
        public readonly string $content,
    ) {
    }
}
