<?php

declare(strict_types=1);

namespace App\Application\Group\CommandHandler;

use App\Application\Group\Command\CreateGroupCommand;
use App\Domain\Group\Entity\Group;
use App\Domain\Group\Repository\GroupRepositoryInterface;
use App\Domain\GroupMembership\Entity\GroupMembership;
use App\Domain\GroupMembership\Repository\GroupMembershipRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(bus: 'command.bus', method: 'handle')]
final class CreateGroupCommandHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly GroupMembershipRepositoryInterface $membershipRepository,
    ) {
    }

    public function handle(CreateGroupCommand $command): array
    {
        $group = Group::create(
            id: Uuid::v4()->toString(),
            name: $command->name,
            invitationCode: $command->invitationCode,
            creatorId: $command->userId,
        );

        $this->groupRepository->save($group);

        $membership = GroupMembership::join(
            id: Uuid::v4()->toString(),
            groupId: $group->getId(),
            userId: $command->userId,
        );

        $this->membershipRepository->save($membership);

        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'invitationCode' => $group->getInvitationCode(),
        ];
    }
}
