<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\RegisterUserCommand;
use App\Application\User\DTO\RegisterUserResponse;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\BiologicalSex;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Security\PasswordHasher;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus', method: 'handle')]
final class RegisterUserCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasher $passwordHasher,
    ) {
    }

    public function handle(RegisterUserCommand $command): RegisterUserResponse
    {
        $email = Email::fromString($command->email);

        if ($this->userRepository->emailExists($email)) {
            throw UserAlreadyExistsException::withEmail($command->email);
        }

        $birthDate = DateTimeImmutable::createFromFormat('Y-m-d', $command->birthDate);
        if (false === $birthDate) {
            throw new InvalidArgumentException('Invalid birth date format. Expected YYYY-MM-DD');
        }

        $biologicalSex = BiologicalSex::fromString($command->biologicalSex);
        $passwordHash = $this->passwordHasher->hash($command->password);

        $user = User::register(
            id: Uuid::v4()->toString(),
            email: $email,
            passwordHash: $passwordHash,
            firstName: $command->firstName,
            birthDate: $birthDate,
            biologicalSex: $biologicalSex,
        );

        $this->userRepository->save($user);

        return new RegisterUserResponse(
            id: $user->getId(),
            email: $user->getEmail()->getValue(),
            firstName: $user->getFirstName(),
        );
    }
}
