<?php

declare(strict_types=1);

namespace App\Application\Measurement\QueryHandler;

use App\Application\Measurement\DTO\MeasurementResponse;
use App\Application\Measurement\Query\GetMeasurementQuery;
use App\Domain\Measurement\Exception\MeasurementNotFoundException;
use App\Domain\Measurement\Repository\MeasurementRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus', method: 'handle')]
final class GetMeasurementQueryHandler
{
    public function __construct(
        private readonly MeasurementRepositoryInterface $measurementRepository,
    ) {
    }

    public function handle(GetMeasurementQuery $query): MeasurementResponse
    {
        $measurement = $this->measurementRepository->findByIdForUser($query->id, $query->userId);

        if (null === $measurement) {
            throw MeasurementNotFoundException::withId($query->id);
        }

        return MeasurementResponse::fromMeasurement($measurement);
    }
}
