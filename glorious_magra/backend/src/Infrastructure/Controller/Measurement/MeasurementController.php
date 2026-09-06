<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Measurement;

use App\Application\Measurement\Command\CreateMeasurementCommand;
use App\Application\Measurement\DTO\MeasurementResponse;
use App\Application\Measurement\Query\GetMeasurementQuery;
use App\Application\Measurement\Query\ListMeasurementsQuery;
use App\Infrastructure\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use assert;

#[Route('/api/measurements', format: 'json')]
class MeasurementController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'command.bus')]
        private readonly MessageBusInterface $commandBus,
        #[Autowire(service: 'query.bus')]
        private readonly MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $commandBus;
    }

    #[Route('', methods: ['POST'])]
    public function create(CreateMeasurementRequest $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $data = $request->request->all();

        if ([] === $data) {
            $decodedContent = json_decode($request->getContent(), associative: true);
            $data = is_array($decodedContent) ? $decodedContent : [];
        }

        $response = $this->handle(new CreateMeasurementCommand(
            userId: $user->getId(),
            biologicalSex: $user->getBiologicalSex()->value,
            measuredAt: $data['date'] ?? '',
            weight: (float) ($data['weight'] ?? 0),
            height: (float) ($data['height'] ?? 0),
            neck: (float) ($data['neck'] ?? 0),
            waist: (float) ($data['waist'] ?? 0),
            hip: isset($data['hip']) ? (float) $data['hip'] : null,
        ));
        assert($response instanceof MeasurementResponse);

        return new JsonResponse([
            'success' => true,
            'data' => $response->toArray(),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $this->messageBus = $this->queryBus;

        $measurements = $this->handle(new ListMeasurementsQuery(
            userId: $user->getId(),
            from: $request->query->get('from'),
            to: $request->query->get('to'),
        ));

        return new JsonResponse([
            'success' => true,
            'data' => array_map(
                static fn (MeasurementResponse $measurement): array => $measurement->toArray(),
                $measurements,
            ),
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function detail(string $id): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $this->messageBus = $this->queryBus;

        $response = $this->handle(new GetMeasurementQuery($id, $user->getId()));
        assert($response instanceof MeasurementResponse);

        return new JsonResponse([
            'success' => true,
            'data' => $response->toArray(),
        ], JsonResponse::HTTP_OK);
    }

    private function getAuthenticatedUser(): \App\Domain\User\Entity\User
    {
        $user = $this->getUser();

        if (!$user instanceof SymfonyUser) {
            throw new AccessDeniedException('Authentication is required.');
        }

        return $user->getUser();
    }
}
