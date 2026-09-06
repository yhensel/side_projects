<?php

declare(strict_types=1);

namespace App\Domain\GroupMembership\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'glorious_magra_group_memberships')]
class GroupMembership
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $id,

        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $groupId,

        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $userId,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $joinedAt,
    ) {
    }

    public static function join(string $id, string $groupId, string $userId): self
    {
        return new self(
            id: $id,
            groupId: $groupId,
            userId: $userId,
            joinedAt: new \DateTimeImmutable(),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getJoinedAt(): \DateTimeImmutable
    {
        return $this->joinedAt;
    }
}
