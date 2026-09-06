<?php

declare(strict_types=1);

namespace App\Application\Measurement\DTO;

use App\Domain\Measurement\Entity\Measurement;

final class MeasurementResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $date,
        public readonly float $weight,
        public readonly float $height,
        public readonly float $neck,
        public readonly float $waist,
        public readonly ?float $hip,
        public readonly float $calculatedFatPercentage,
    ) {
    }

    public static function fromMeasurement(Measurement $measurement): self
    {
        return new self(
            id: $measurement->getId(),
            date: $measurement->getMeasuredAt()->format('Y-m-d'),
            weight: $measurement->getWeight(),
            height: $measurement->getHeight(),
            neck: $measurement->getNeck(),
            waist: $measurement->getWaist(),
            hip: $measurement->getHip(),
            calculatedFatPercentage: $measurement->getCalculatedFatPercentage(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'weight' => $this->weight,
            'height' => $this->height,
            'neck' => $this->neck,
            'waist' => $this->waist,
            'hip' => $this->hip,
            'calculatedFatPercentage' => $this->calculatedFatPercentage,
        ];
    }
}
