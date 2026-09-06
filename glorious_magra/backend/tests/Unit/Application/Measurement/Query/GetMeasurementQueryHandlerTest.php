<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Measurement\Query;

use App\Application\Measurement\DTO\MeasurementResponse;
use App\Application\Measurement\Query\GetMeasurementQuery;
use App\Application\Measurement\QueryHandler\GetMeasurementQueryHandler;
use App\Domain\Measurement\Entity\Measurement;
use App\Domain\Measurement\Exception\MeasurementNotFoundException;
use App\Domain\Measurement\Repository\MeasurementRepositoryInterface;
use PHPUnit\Framework\TestCase;

class GetMeasurementQueryHandlerTest extends TestCase
{
    public function testItReturnsMeasurementForUser(): void
    {
        $measurement = Measurement::record(
            id: 'measurement-123',
            userId: 'user-123',
            measuredAt: new \DateTimeImmutable('2026-07-10'),
            weight: 82.5,
            height: 180.0,
            neck: 40.0,
            waist: 86.0,
            hip: 95.0,
            calculatedFatPercentage: 18.25,
        );

        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByIdForUser')
            ->with('measurement-123', 'user-123')
            ->willReturn($measurement);

        $handler = new GetMeasurementQueryHandler($repository);

        $response = $handler->handle(new GetMeasurementQuery('measurement-123', 'user-123'));

        $this->assertInstanceOf(MeasurementResponse::class, $response);
        $this->assertSame('measurement-123', $response->id);
        $this->assertSame('2026-07-10', $response->date);
        $this->assertSame(18.25, $response->calculatedFatPercentage);
    }

    public function testItThrowsWhenMeasurementDoesNotBelongToUser(): void
    {
        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByIdForUser')
            ->with('measurement-123', 'user-123')
            ->willReturn(null);

        $handler = new GetMeasurementQueryHandler($repository);

        $this->expectException(MeasurementNotFoundException::class);

        $handler->handle(new GetMeasurementQuery('measurement-123', 'user-123'));
    }
}
