<?php

declare(strict_types=1);

namespace App\Application\Group\CommandHandler;

use App\Application\Group\Command\JoinGroupCommand;
use App\Domain\Group\Repository\GroupRepositoryInterface;
use App\Domain\GroupMembership\Entity\GroupMembership;
use App\Domain\GroupMembership\Repository\GroupMembershipRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(bus: 'command.bus', method: 'handle')]
final class JoinGroupCommandHandler
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly GroupMembershipRepositoryInterface $membershipRepository,
    ) {
    }

    public function handle(JoinGroupCommand $command): array
    {
        $group = $this->groupRepository->findByInvitationCode($command->invitationCode);

        if (null === $group) {
            throw new \InvalidArgumentException('Group invitation code not found.');
        }

        $existingMembership = $this->membershipRepository->findByUserAndGroup($command->userId, $group->getId());
        if (null !== $existingMembership) {
            throw new \InvalidArgumentException('You are already a member of this group.');
        }

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
