<?php

declare(strict_types=1);

namespace App\Application\Measurement\Query;

final class GetMeasurementQuery
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
    ) {
    }
}
