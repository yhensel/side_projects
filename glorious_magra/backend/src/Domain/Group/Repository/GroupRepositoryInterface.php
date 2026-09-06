<?php

declare(strict_types=1);

namespace App\Domain\Group\Repository;

use App\Domain\Group\Entity\Group;

interface GroupRepositoryInterface
{
    public function save(Group $group): void;

    public function findById(string $id): ?Group;

    public function findByInvitationCode(string $invitationCode): ?Group;
}
