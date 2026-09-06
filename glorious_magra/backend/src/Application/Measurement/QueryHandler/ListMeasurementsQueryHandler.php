<?php

declare(strict_types=1);

namespace App\Application\Measurement\QueryHandler;

use App\Application\Measurement\DTO\MeasurementResponse;
use App\Application\Measurement\Query\ListMeasurementsQuery;
use App\Domain\Measurement\Repository\MeasurementRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus', method: 'handle')]
final class ListMeasurementsQueryHandler
{
    public function __construct(
        private readonly MeasurementRepositoryInterface $measurementRepository,
    ) {
    }

    /**
     * @return MeasurementResponse[]
     */
    public function handle(ListMeasurementsQuery $query): array
    {
        $from = $this->createDateOrNull($query->from, 'from');
        $to = $this->createDateOrNull($query->to, 'to');

        if (null !== $to) {
            $to = $to->setTime(23, 59, 59);
        }

        if (null !== $from && null !== $to && $from > $to) {
            throw new \InvalidArgumentException('The "from" date must be before or equal to the "to" date.');
        }

        return array_map(
            static fn ($measurement): MeasurementResponse => MeasurementResponse::fromMeasurement($measurement),
            $this->measurementRepository->findByUserId($query->userId, $from, $to),
        );
    }

    private function createDateOrNull(?string $date, string $field): ?\DateTimeImmutable
    {
        if (null === $date || '' === $date) {
            return null;
        }

        $dateTime = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (false === $dateTime) {
            throw new \InvalidArgumentException(sprintf('Invalid "%s" date format. Expected YYYY-MM-DD', $field));
        }

        return $dateTime;
    }
}
