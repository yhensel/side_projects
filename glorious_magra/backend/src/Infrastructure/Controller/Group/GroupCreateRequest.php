<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Group;

use App\Infrastructure\Controller\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class GroupCreateRequest extends BaseRequest
{
    public function getRules(): array
    {
        return [
            new Assert\Collection([
                'name' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 100),
                ]),
                'invitationCode' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Length(min: 4, max: 16),
                ]),
            ], allowExtraFields: true),
        ];
    }
}
