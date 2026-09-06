<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\BiologicalSex;
use App\Domain\User\ValueObject\Email;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository::class)]
#[ORM\Table(name: 'glorious_magra_users')]
class User
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $id,

        #[ORM\Column(type: 'email', length: 255, unique: true)]
        private readonly Email $email,

        #[ORM\Column(type: 'string', length: 255)]
        private readonly string $passwordHash,

        #[ORM\Column(type: 'string', length: 255)]
        private readonly string $firstName,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $birthDate,

        #[ORM\Column(type: 'string', enumType: BiologicalSex::class)]
        private readonly BiologicalSex $biologicalSex,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public static function register(
        string $id,
        Email $email,
        string $passwordHash,
        string $firstName,
        \DateTimeImmutable $birthDate,
        BiologicalSex $biologicalSex,
    ): self {
        return new self(
            id: $id,
            email: $email,
            passwordHash: $passwordHash,
            firstName: $firstName,
            birthDate: $birthDate,
            biologicalSex: $biologicalSex,
            createdAt: new \DateTimeImmutable(),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function getBiologicalSex(): BiologicalSex
    {
        return $this->biologicalSex;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function verifyPassword(string $plainPassword, callable $verifier): bool
    {
        return $verifier($plainPassword, $this->passwordHash);
    }
}
