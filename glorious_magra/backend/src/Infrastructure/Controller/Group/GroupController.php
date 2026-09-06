<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Group;

use App\Application\Group\Command\CreateGroupCommand;
use App\Application\Group\Command\JoinGroupCommand;
use App\Infrastructure\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/api/groups', format: 'json')]
class GroupController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    #[Route('', methods: ['POST'])]
    public function create(GroupCreateRequest $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        $response = $this->handle(new CreateGroupCommand(
            userId: $user->getId(),
            name: $request->request->get('name', ''),
            invitationCode: $request->request->get('invitationCode', ''),
        ));

        return new JsonResponse([
            'success' => true,
            'data' => $response,
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/join', methods: ['POST'])]
    public function join(GroupJoinRequest $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        $response = $this->handle(new JoinGroupCommand(
            userId: $user->getId(),
            invitationCode: $request->request->get('invitationCode', ''),
        ));

        return new JsonResponse([
            'success' => true,
            'data' => $response,
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
