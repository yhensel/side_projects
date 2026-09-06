<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\LoginUserCommand;
use App\Application\User\DTO\LoginUserResponse;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Security\PasswordHasher;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use App\Infrastructure\Security\SymfonyUser;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus', method: 'handle')]
final class LoginUserCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasher $passwordHasher,
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
    ) {
    }

    public function handle(LoginUserCommand $command): LoginUserResponse
    {
        $email = Email::fromString($command->email);
        $user = $this->userRepository->findByEmail($email);

        if (null === $user) {
            throw new AuthenticationException('Invalid credentials');
        }

        if (!$this->passwordHasher->verify($command->password, $user->getPasswordHash())) {
            throw new AuthenticationException('Invalid credentials');
        }

        $symfonyUser = new SymfonyUser($user);
        $token = $this->tokenManager->create($symfonyUser);
        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($symfonyUser, 2592000);
        $this->refreshTokenManager->save($refreshToken);

        return new LoginUserResponse(
            token: $token,
            userId: $user->getId(),
            email: $user->getEmail()->getValue(),
            firstName: $user->getFirstName(),
            biologicalSex: $user->getBiologicalSex()->value,
            refreshToken: (string) $refreshToken,
        );
    }
}
