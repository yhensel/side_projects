<?php

declare(strict_types=1);

namespace App\Domain\GroupMembership\Repository;

use App\Domain\GroupMembership\Entity\GroupMembership;

interface GroupMembershipRepositoryInterface
{
    public function save(GroupMembership $membership): void;

    public function findByUserAndGroup(string $userId, string $groupId): ?GroupMembership;

    public function findByUserId(string $userId): array;

    public function findByGroupId(string $groupId): array;
}
