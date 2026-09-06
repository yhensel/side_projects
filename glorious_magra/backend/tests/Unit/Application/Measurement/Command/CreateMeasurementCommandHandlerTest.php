<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Measurement\Command;

use App\Application\Measurement\Command\CreateMeasurementCommand;
use App\Application\Measurement\CommandHandler\CreateMeasurementCommandHandler;
use App\Application\Measurement\DTO\MeasurementResponse;
use App\Domain\Measurement\Entity\Measurement;
use App\Domain\Measurement\Repository\MeasurementRepositoryInterface;
use App\Domain\Measurement\Service\BodyFatCalculationService;
use App\Domain\User\ValueObject\BiologicalSex;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateMeasurementCommandHandlerTest extends TestCase
{
    public function testItCreatesMeasurementAndReturnsResponse(): void
    {
        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Measurement $measurement): bool {
                return 'user-123' === $measurement->getUserId()
                    && '2026-07-10' === $measurement->getMeasuredAt()->format('Y-m-d')
                    && 82.5 === $measurement->getWeight()
                    && 18.25 === $measurement->getCalculatedFatPercentage();
            }));

        $calculationService = $this->createMock(BodyFatCalculationService::class);
        $calculationService->expects($this->once())
            ->method('calculate')
            ->with(
                BiologicalSex::MALE,
                180.0,
                40.0,
                86.0,
                0.0,
            )
            ->willReturn(18.25);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->willReturn(new \Symfony\Component\Messenger\Envelope(new \stdClass()));

        $handler = new CreateMeasurementCommandHandler($repository, $calculationService, $messageBus);

        $response = $handler->handle(new CreateMeasurementCommand(
            userId: 'user-123',
            biologicalSex: 'male',
            measuredAt: '2026-07-10',
            weight: 82.5,
            height: 180.0,
            neck: 40.0,
            waist: 86.0,
            hip: null,
        ));

        $this->assertInstanceOf(MeasurementResponse::class, $response);
        $this->assertSame('2026-07-10', $response->date);
        $this->assertSame(82.5, $response->weight);
        $this->assertSame(18.25, $response->calculatedFatPercentage);
    }

    public function testItRejectsInvalidMeasurementDate(): void
    {
        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->never())->method('save');

        $calculationService = $this->createMock(BodyFatCalculationService::class);
        $calculationService->expects($this->never())->method('calculate');

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->never())->method('dispatch');

        $handler = new CreateMeasurementCommandHandler($repository, $calculationService, $messageBus);

        $this->expectException(\InvalidArgumentException::class);

        $handler->handle(new CreateMeasurementCommand(
            userId: 'user-123',
            biologicalSex: 'male',
            measuredAt: '10-07-2026',
            weight: 82.5,
            height: 180.0,
            neck: 40.0,
            waist: 86.0,
            hip: 95.0,
        ));
    }

    public function testItRequiresHipMeasurementForFemaleCalculation(): void
    {
        $repository = $this->createMock(MeasurementRepositoryInterface::class);
        $repository->expects($this->never())->method('save');

        $calculationService = $this->createMock(BodyFatCalculationService::class);
        $calculationService->expects($this->never())->method('calculate');

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->never())->method('dispatch');

        $handler = new CreateMeasurementCommandHandler($repository, $calculationService, $messageBus);

        $this->expectException(\InvalidArgumentException::class);

        $handler->handle(new CreateMeasurementCommand(
            userId: 'user-123',
            biologicalSex: 'female',
            measuredAt: '2026-07-10',
            weight: 65.0,
            height: 165.0,
            neck: 32.0,
            waist: 72.0,
            hip: null,
        ));
    }
}
