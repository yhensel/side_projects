<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Auth;

use App\Application\User\Command\LoginUserCommand;
use App\Application\User\DTO\LoginUserResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use assert;

#[Route('/api/auth', format: 'json')]
class LoginController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'command.bus')]
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/login', methods: ['POST'])]
    public function login(LoginRequest $request): JsonResponse
    {
        $command = new LoginUserCommand(
            email: $request->request->get('email', ''),
            password: $request->request->get('password', ''),
        );

        $response = $this->handle($command);
        assert($response instanceof LoginUserResponse);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'token' => $response->token,
                'user' => [
                    'id' => $response->userId,
                    'email' => $response->email,
                    'firstName' => $response->firstName,
                    'biologicalSex' => $response->biologicalSex,
                    'refreshToken' => $response->refreshToken,
                ],
            ],
        ], JsonResponse::HTTP_OK);
    }
}
