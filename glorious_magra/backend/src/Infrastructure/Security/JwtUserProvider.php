<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JwtUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasher $passwordHasher,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $email = Email::fromString($identifier);
        } catch (\InvalidArgumentException $e) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        $user = $this->userRepository->findByEmail($email);

        if (null === $user) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        return new SymfonyUser($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SymfonyUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }

        $email = $user->getUser()->getEmail();
        return $this->loadUserByIdentifier($email->getValue());
    }

    public function supportsClass(string $class): bool
    {
        return SymfonyUser::class === $class;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $hashedPassword): void
    {
        // This would be called if you want to upgrade password hashing algorithm
        // Not implemented for now as we handle password hashing at registration
    }
}
