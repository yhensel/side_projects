<?php

declare(strict_types=1);

namespace App\Application\Activity\CommandHandler;

use App\Application\Activity\Command\CreateActivityCommand;
use App\Domain\Activity\Entity\Activity;
use App\Domain\Activity\Repository\ActivityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(bus: 'command.bus', method: 'handle')]
final class CreateActivityCommandHandler
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
    ) {
    }

    public function handle(CreateActivityCommand $command): void
    {
        $activity = Activity::create(
            id: Uuid::v4()->toString(),
            userId: $command->userId,
            groupId: $command->groupId,
            content: $command->content,
        );

        $this->activityRepository->save($activity);
    }
}
