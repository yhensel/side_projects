<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Measurement;

use App\Infrastructure\Controller\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateMeasurementRequest extends BaseRequest
{
    public function getRules(): array
    {
        return [
            new Assert\Collection([
                'date' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Date(),
                ]),
                'weight' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\Positive(),
                ]),
                'height' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\Positive(),
                ]),
                'neck' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\Positive(),
                ]),
                'waist' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\Positive(),
                ]),
                'hip' => new Assert\Optional([
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\Positive(),
                ]),
            ], allowExtraFields: true),
        ];
    }
}
