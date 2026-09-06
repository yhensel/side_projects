<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Group;

use App\Infrastructure\Controller\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class GroupJoinRequest extends BaseRequest
{
    public function getRules(): array
    {
        return [
            new Assert\Collection([
                'invitationCode' => new Assert\Required([
                    new Assert\NotBlank(),
                    new Assert\Length(min: 4, max: 16),
                ]),
            ], allowExtraFields: true),
        ];
    }
}
