<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Measurement\Query;

use App\Application\Measurement\DTO\MeasurementResponse;
use App\Application\Measurement\Query\ListMeasurementsQuery;
use App\Application\Measurement\QueryHandler\ListMeasurementsQueryHandler;
use App\Domain\Measurement\Entity\Measurement;
use App\Domain\Measurement\Repository\MeasurementRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ListMeasurementsQueryHandlerTest extends TestCase
{
    public function testItReturnsMeasurementsForUser(): void
    {
        $firstMeasurement = Measurement::record(
            id: 'measurement-1',
            userId: 'user-123',
            measuredAt: new \DateTimeImmutable('2026-07-10'),
            weight: 82.5,
            height: 180.0,
            neck: 40.0,
            waist: 86.0,
            hip: 95.0,
            calculatedFatPercentage: 18.25,
        );
        $secondMeasurement = Measurement::record(
            id: 'measurement-2',
            userId: 'user-123',
            measuredAt: new \DateTimeImmutable('2026-07-03'),
            weight: 83.0,
            height: 180.0,
            neck: 40.0,
            waist: 87.0,
            hip: 95.0,
            calculatedFatPercentage: 19.10,
        );

        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByUserId')
            ->with('user-123', null, null)
            ->willReturn([$firstMeasurement, $secondMeasurement]);

        $handler = new ListMeasurementsQueryHandler($repository);

        $responses = $handler->handle(new ListMeasurementsQuery('user-123'));

        $this->assertCount(2, $responses);
        $this->assertContainsOnlyInstancesOf(MeasurementResponse::class, $responses);
        $this->assertSame('measurement-1', $responses[0]->id);
        $this->assertSame('measurement-2', $responses[1]->id);
    }

    public function testItFiltersMeasurementsByDateRange(): void
    {
        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByUserId')
            ->with(
                'user-123',
                $this->callback(static fn (\DateTimeImmutable $date): bool => '2026-07-01 00:00:00' === $date->format('Y-m-d H:i:s')),
                $this->callback(static fn (\DateTimeImmutable $date): bool => '2026-07-31 23:59:59' === $date->format('Y-m-d H:i:s')),
            )
            ->willReturn([]);

        $handler = new ListMeasurementsQueryHandler($repository);

        $this->assertSame([], $handler->handle(new ListMeasurementsQuery('user-123', '2026-07-01', '2026-07-31')));
    }

    public function testItRejectsInvalidDateRange(): void
    {
        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->never())->method('findByUserId');

        $handler = new ListMeasurementsQueryHandler($repository);

        $this->expectException(\InvalidArgumentException::class);

        $handler->handle(new ListMeasurementsQuery('user-123', '2026-08-01', '2026-07-31'));
    }

    public function testItReturnsEmptyListWhenUserHasNoMeasurements(): void
    {
        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByUserId')
            ->with('user-123', null, null)
            ->willReturn([]);

        $handler = new ListMeasurementsQueryHandler($repository);

        $this->assertSame([], $handler->handle(new ListMeasurementsQuery('user-123')));
    }
}
