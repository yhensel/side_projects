<?php

declare(strict_types=1);

namespace App\Application\Measurement\Query;

final class ListMeasurementsQuery
{
    public function __construct(
        public readonly string $userId,
        public readonly ?string $from = null,
        public readonly ?string $to = null,
    ) {
    }
}
