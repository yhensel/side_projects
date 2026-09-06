<?php

declare(strict_types=1);

namespace App\Application\Measurement\CommandHandler;

use App\Application\Activity\Command\CreateActivityCommand;
use App\Application\Measurement\Command\CreateMeasurementCommand;
use App\Application\Measurement\DTO\MeasurementResponse;
use App\Domain\Measurement\Entity\Measurement;
use App\Domain\Measurement\Repository\MeasurementRepositoryInterface;
use App\Domain\Measurement\Service\BodyFatCalculationService;
use App\Domain\User\ValueObject\BiologicalSex;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;
use InvalidArgumentException;

#[AsMessageHandler(bus: 'command.bus', method: 'handle')]
final class CreateMeasurementCommandHandler
{
    public function __construct(
        private readonly MeasurementRepositoryInterface $measurementRepository,
        private readonly BodyFatCalculationService $bodyFatCalculationService,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function handle(CreateMeasurementCommand $command): MeasurementResponse
    {
        $measuredAt = DateTimeImmutable::createFromFormat('Y-m-d', $command->measuredAt);
        if (false === $measuredAt) {
            throw new InvalidArgumentException('Invalid measurement date format. Expected YYYY-MM-DD');
        }

        $biologicalSex = BiologicalSex::fromString($command->biologicalSex);
        $hip = $command->hip ?? 0.0;

        if (BiologicalSex::FEMALE === $biologicalSex && null === $command->hip) {
            throw new InvalidArgumentException('Hip measurement is required for female body fat calculation.');
        }

        $calculatedFatPercentage = $this->bodyFatCalculationService->calculate(
            biologicalSex: $biologicalSex,
            height: $command->height,
            neck: $command->neck,
            waist: $command->waist,
            hip: $hip,
        );

        $measurement = Measurement::record(
            id: Uuid::v4()->toString(),
            userId: $command->userId,
            measuredAt: $measuredAt,
            weight: $command->weight,
            height: $command->height,
            neck: $command->neck,
            waist: $command->waist,
            hip: $command->hip,
            calculatedFatPercentage: $calculatedFatPercentage,
        );

        $this->measurementRepository->save($measurement);

        $this->messageBus->dispatch(new CreateActivityCommand(
            userId: $command->userId,
            groupId: null,
            content: sprintf('logged a new measurement on %s.', $measurement->getMeasuredAt()->format('Y-m-d')),
        ));

        return MeasurementResponse::fromMeasurement($measurement);
    }
}
