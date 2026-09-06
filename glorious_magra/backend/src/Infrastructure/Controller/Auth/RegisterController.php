<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Auth;

use App\Application\User\Command\RegisterUserCommand;
use App\Application\User\DTO\RegisterUserResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use assert;

#[Route('/api/auth', format: 'json')]
class RegisterController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'command.bus')]
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/register', methods: ['POST'])]
    public function register(RegisterRequest $request): JsonResponse
    {
        $command = new RegisterUserCommand(
            email: $request->request->get('email', ''),
            password: $request->request->get('password', ''),
            firstName: $request->request->get('firstName', ''),
            birthDate: $request->request->get('birthDate', ''),
            biologicalSex: $request->request->get('biologicalSex', ''),
        );

        $response = $this->handle($command);
        assert($response instanceof RegisterUserResponse);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'id' => $response->id,
                'email' => $response->email,
                'firstName' => $response->firstName,
            ],
        ], JsonResponse::HTTP_CREATED);
    }
}
