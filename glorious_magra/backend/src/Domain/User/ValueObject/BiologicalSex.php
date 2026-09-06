<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

enum BiologicalSex: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public static function fromString(string $value): self
    {
        return match (strtolower($value)) {
            'male' => self::MALE,
            'female' => self::FEMALE,
            default => throw new \InvalidArgumentException(
                sprintf('Invalid biological sex: %s. Expected "male" or "female".', $value)
            ),
        };
    }
}
