<?php

declare(strict_types=1);

namespace App\Domain\Measurement\Exception;

class MeasurementNotFoundException extends \Exception
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Measurement "%s" was not found.', $id));
    }
}
