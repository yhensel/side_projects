<?php

declare(strict_types=1);

namespace App\Domain\Activity\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'glorious_magra_activities')]
class Activity
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $id,

        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $userId,

        #[ORM\Column(type: 'string', length: 36, nullable: true)]
        private readonly ?string $groupId,

        #[ORM\Column(type: 'text')]
        private readonly string $content,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(string $id, string $userId, ?string $groupId, string $content): self
    {
        return new self(
            id: $id,
            userId: $userId,
            groupId: $groupId,
            content: $content,
            createdAt: new \DateTimeImmutable(),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
