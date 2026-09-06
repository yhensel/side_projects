<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Group\Command;

use App\Application\Group\Command\CreateGroupCommand;
use App\Application\Group\Command\JoinGroupCommand;
use App\Application\Group\CommandHandler\CreateGroupCommandHandler;
use App\Application\Group\CommandHandler\JoinGroupCommandHandler;
use App\Domain\Group\Entity\Group;
use App\Domain\Group\Repository\GroupRepositoryInterface;
use App\Domain\GroupMembership\Entity\GroupMembership;
use App\Domain\GroupMembership\Repository\GroupMembershipRepositoryInterface;
use PHPUnit\Framework\TestCase;

class GroupCommandHandlerTest extends TestCase
{
    public function testItCreatesGroupAndCreatorMembership(): void
    {
        $groupRepository = $this->createMock(GroupRepositoryInterface::class);
        $membershipRepository = $this->createMock(GroupMembershipRepositoryInterface::class);

        $groupRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Group $group): bool => 'Test Group' === $group->getName() && 'ABC123' === $group->getInvitationCode()));

        $membershipRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (GroupMembership $membership): bool => 'user-1' === $membership->getUserId()));

        $handler = new CreateGroupCommandHandler($groupRepository, $membershipRepository);

        $response = $handler->handle(new CreateGroupCommand(
            userId: 'user-1',
            name: 'Test Group',
            invitationCode: 'ABC123',
        ));

        $this->assertSame('Test Group', $response['name']);
        $this->assertSame('ABC123', $response['invitationCode']);
        $this->assertArrayHasKey('id', $response);
    }

    public function testItJoinsExistingGroupForNewMember(): void
    {
        $groupRepository = $this->createMock(GroupRepositoryInterface::class);
        $membershipRepository = $this->createMock(GroupMembershipRepositoryInterface::class);

        $group = Group::create('group-1', 'Runner Circle', 'ABC123', 'creator-1');

        $groupRepository->expects($this->once())
            ->method('findByInvitationCode')
            ->with('ABC123')
            ->willReturn($group);

        $membershipRepository->expects($this->once())
            ->method('findByUserAndGroup')
            ->with('user-2', 'group-1')
            ->willReturn(null);

        $membershipRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (GroupMembership $membership): bool => 'user-2' === $membership->getUserId() && 'group-1' === $membership->getGroupId()));

        $handler = new JoinGroupCommandHandler($groupRepository, $membershipRepository);

        $response = $handler->handle(new JoinGroupCommand(
            userId: 'user-2',
            invitationCode: 'ABC123',
        ));

        $this->assertSame('Runner Circle', $response['name']);
        $this->assertSame('ABC123', $response['invitationCode']);
    }
}
