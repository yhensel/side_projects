<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use App\Infrastructure\Controller\Request\BaseRequest;

final class RegisterRequest extends BaseRequest
{
    public function getRules(): array
    {
        return [
            new Assert\Collection([
                'firstName' => new Assert\Required([
                    new Assert\NotBlank(),
                ]),
                'email' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]),
                'password' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8]),
                ]),
                'birthDate' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Date(),
                ]),
                'biologicalSex' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Choice(['male', 'female']),
                ]),
            ], allowExtraFields: true),
        ];
    }
}