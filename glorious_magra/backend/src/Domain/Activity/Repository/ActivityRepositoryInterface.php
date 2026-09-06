<?php

declare(strict_types=1);

namespace App\Domain\Activity\Repository;

use App\Domain\Activity\Entity\Activity;

interface ActivityRepositoryInterface
{
    public function save(Activity $activity): void;

    public function findByGroupId(string $groupId): array;
}
