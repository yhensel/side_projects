<?php

declare(strict_types=1);

namespace App\Domain\Measurement\Entity;

use App\Infrastructure\Persistence\Doctrine\Repository\DoctrineMeasurementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineMeasurementRepository::class)]
#[ORM\Table(name: 'glorious_magra_measurements')]
#[ORM\Index(columns: ['user_id', 'measured_at'], name: 'idx_measurements_user_measured_at')]
class Measurement
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $id,

        #[ORM\Column(type: 'string', length: 36)]
        private readonly string $userId,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $measuredAt,

        #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
        private readonly string $weight,

        #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
        private readonly string $height,

        #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
        private readonly string $neck,

        #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
        private readonly string $waist,

        #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: true)]
        private readonly ?string $hip,

        #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
        private readonly string $calculatedFatPercentage,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public static function record(
        string $id,
        string $userId,
        \DateTimeImmutable $measuredAt,
        float $weight,
        float $height,
        float $neck,
        float $waist,
        ?float $hip,
        float $calculatedFatPercentage,
    ): self {
        return new self(
            id: $id,
            userId: $userId,
            measuredAt: $measuredAt,
            weight: self::formatDecimal($weight),
            height: self::formatDecimal($height),
            neck: self::formatDecimal($neck),
            waist: self::formatDecimal($waist),
            hip: null === $hip ? null : self::formatDecimal($hip),
            calculatedFatPercentage: self::formatDecimal($calculatedFatPercentage),
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

    public function getMeasuredAt(): \DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function getWeight(): float
    {
        return (float) $this->weight;
    }

    public function getHeight(): float
    {
        return (float) $this->height;
    }

    public function getNeck(): float
    {
        return (float) $this->neck;
    }

    public function getWaist(): float
    {
        return (float) $this->waist;
    }

    public function getHip(): ?float
    {
        return null === $this->hip ? null : (float) $this->hip;
    }

    public function getCalculatedFatPercentage(): float
    {
        return (float) $this->calculatedFatPercentage;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    private static function formatDecimal(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
