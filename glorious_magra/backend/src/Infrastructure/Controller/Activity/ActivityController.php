<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Activity;

use App\Domain\Activity\Repository\ActivityRepositoryInterface;
use App\Domain\GroupMembership\Repository\GroupMembershipRepositoryInterface;
use App\Infrastructure\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/api/activities', format: 'json')]
class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly GroupMembershipRepositoryInterface $membershipRepository,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $memberships = $this->membershipRepository->findByUserId($user->getId());
        $groupIds = array_map(static fn ($membership): string => $membership->getGroupId(), $memberships);

        $activities = [];
        foreach ($groupIds as $groupId) {
            $activities = [...$activities, ...$this->activityRepository->findByGroupId($groupId)];
        }

        usort($activities, static fn ($left, $right): int => $right->getCreatedAt() <=> $left->getCreatedAt());

        return new JsonResponse([
            'success' => true,
            'data' => array_map(
                static fn ($activity): array => [
                    'id' => $activity->getId(),
                    'content' => $activity->getContent(),
                    'createdAt' => $activity->getCreatedAt()->format('Y-m-d H:i:s'),
                ],
                $activities,
            ),
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
