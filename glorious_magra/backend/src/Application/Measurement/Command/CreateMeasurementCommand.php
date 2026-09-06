<?php

declare(strict_types=1);

namespace App\Application\Measurement\Command;

final class CreateMeasurementCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $biologicalSex,
        public readonly string $measuredAt,
        public readonly float $weight,
        public readonly float $height,
        public readonly float $neck,
        public readonly float $waist,
        public readonly ?float $hip,
    ) {
    }
}
