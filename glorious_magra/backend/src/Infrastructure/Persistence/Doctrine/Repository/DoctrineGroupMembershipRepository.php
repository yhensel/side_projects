<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\GroupMembership\Entity\GroupMembership;
use App\Domain\GroupMembership\Repository\GroupMembershipRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupMembership>
 */
class DoctrineGroupMembershipRepository extends ServiceEntityRepository implements GroupMembershipRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMembership::class);
    }

    public function save(GroupMembership $membership): void
    {
        $this->getEntityManager()->persist($membership);
        $this->getEntityManager()->flush();
    }

    public function findByUserAndGroup(string $userId, string $groupId): ?GroupMembership
    {
        return $this->findOneBy([
            'userId' => $userId,
            'groupId' => $groupId,
        ]);
    }

    public function findByUserId(string $userId): array
    {
        return $this->findBy(['userId' => $userId]);
    }

    public function findByGroupId(string $groupId): array
    {
        return $this->findBy(['groupId' => $groupId]);
    }
}
