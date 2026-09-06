<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\User\Command;

use App\Application\User\Command\RegisterUserCommand;
use App\Application\User\CommandHandler\RegisterUserCommandHandler;
use App\Application\User\DTO\RegisterUserResponse;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Security\PasswordHasher;
use PHPUnit\Framework\TestCase;

class RegisterUserCommandHandlerTest extends TestCase
{
    public function testItRegistersANewUser(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('emailExists')
            ->with($this->isInstanceOf(Email::class))
            ->willReturn(false);

        $repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $passwordHasher = $this->createMock(PasswordHasher::class);
        $passwordHasher->expects($this->once())
            ->method('hash')
            ->willReturn('hashed-password');

        $handler = new RegisterUserCommandHandler($repository, $passwordHasher);
        $command = new RegisterUserCommand(
            email: 'john@example.com',
            password: 'Secret123',
            firstName: 'John',
            birthDate: '1990-01-01',
            biologicalSex: 'male',
        );

        $response = $handler->handle($command);

        $this->assertInstanceOf(RegisterUserResponse::class, $response);
        $this->assertSame('john@example.com', $response->email);
        $this->assertSame('John', $response->firstName);
    }

    public function testItThrowsWhenEmailAlreadyExists(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->method('emailExists')->willReturn(true);

        $passwordHasher = $this->createMock(PasswordHasher::class);
        $handler = new RegisterUserCommandHandler($repository, $passwordHasher);
        $command = new RegisterUserCommand(
            email: 'john@example.com',
            password: 'Secret123',
            firstName: 'John',
            birthDate: '1990-01-01',
            biologicalSex: 'male',
        );

        $this->expectException(UserAlreadyExistsException::class);
        $handler->handle($command);
    }
}
