<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Measurement\Entity\Measurement;
use App\Domain\Measurement\Repository\MeasurementRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Measurement>
 */
class DoctrineMeasurementRepository extends ServiceEntityRepository implements MeasurementRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Measurement::class);
    }

    public function save(Measurement $measurement): void
    {
        $this->getEntityManager()->persist($measurement);
        $this->getEntityManager()->flush();
    }

    public function findByIdForUser(string $id, string $userId): ?Measurement
    {
        return $this->findOneBy([
            'id' => $id,
            'userId' => $userId,
        ]);
    }

    public function findByUserId(
        string $userId,
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): array
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->where('m.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('m.measuredAt', 'DESC');

        if (null !== $from) {
            $queryBuilder
                ->andWhere('m.measuredAt >= :from')
                ->setParameter('from', $from);
        }

        if (null !== $to) {
            $queryBuilder
                ->andWhere('m.measuredAt <= :to')
                ->setParameter('to', $to);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
