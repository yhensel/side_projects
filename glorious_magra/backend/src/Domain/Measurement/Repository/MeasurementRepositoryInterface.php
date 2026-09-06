<?php

declare(strict_types=1);

namespace App\Domain\Measurement\Repository;

use App\Domain\Measurement\Entity\Measurement;

interface MeasurementRepositoryInterface
{
    public function save(Measurement $measurement): void;

    public function findByIdForUser(string $id, string $userId): ?Measurement;

    /**
     * @return Measurement[]
     */
    public function findByUserId(
        string $userId,
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): array;
}
