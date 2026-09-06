<?php

declare(strict_types=1);

namespace App\Domain\Group\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'glorious_magra_community_groups')]
class Group
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $id,

        #[ORM\Column(type: 'string', length: 255)]
        private readonly string $name,

        #[ORM\Column(type: 'string', length: 16)]
        private readonly string $invitationCode,

        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $creatorId,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(string $id, string $name, string $invitationCode, string $creatorId): self
    {
        return new self(
            id: $id,
            name: $name,
            invitationCode: $invitationCode,
            creatorId: $creatorId,
            createdAt: new \DateTimeImmutable(),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInvitationCode(): string
    {
        return $this->invitationCode;
    }

    public function getCreatorId(): string
    {
        return $this->creatorId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
