<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Domain\Activity\Entity\Activity;
use App\Domain\Group\Entity\Group;
use App\Domain\GroupMembership\Entity\GroupMembership;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\BiologicalSex;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Security\PasswordHasher;
use App\Infrastructure\Security\SymfonyUser;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class GroupAndActivityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->truncateTables();
        $this->createAuthenticatedUser();
    }

    protected function tearDown(): void
    {
        $this->truncateTables();
        parent::tearDown();
    }

    public function testItCreatesGroupAndListsActivities(): void
    {
        $this->client->request(
            'POST',
            '/api/groups',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
            content: json_encode(['name' => 'Team Alpha', 'invitationCode' => 'ALPHA1'], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $createdGroup = $this->decodeResponse();
        $this->assertSame('Team Alpha', $createdGroup['data']['name']);

        $activity = Activity::create(
            id: Uuid::v4()->toString(),
            userId: $this->user->getId(),
            groupId: $createdGroup['data']['id'],
            content: 'joined the group',
        );

        $this->entityManager->persist($activity);
        $this->entityManager->flush();

        $this->client->request(
            'GET',
            '/api/activities',
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $activityData = $this->decodeResponse();
        $this->assertCount(1, $activityData['data']);
        $this->assertSame('joined the group', $activityData['data'][0]['content']);
    }

    public function testItJoinsGroupByInvitationCode(): void
    {
        $group = Group::create(Uuid::v4()->toString(), 'Team Beta', 'BETA42', 'creator-1');
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/api/groups/join',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
            content: json_encode(['invitationCode' => 'BETA42'], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $joinedData = $this->decodeResponse();
        $this->assertSame('Team Beta', $joinedData['data']['name']);

        $membership = $this->entityManager->getRepository(GroupMembership::class)->findOneBy([
            'userId' => $this->user->getId(),
            'groupId' => $group->getId(),
        ]);

        $this->assertNotNull($membership);
    }

    private function createAuthenticatedUser(): void
    {
        $passwordHasher = static::getContainer()->get(PasswordHasher::class);
        $tokenManager = static::getContainer()->get(JWTTokenManagerInterface::class);

        $this->user = User::register(
            id: Uuid::v4()->toString(),
            email: Email::fromString('group-'.uniqid('', true).'@example.com'),
            passwordHash: $passwordHasher->hash('secret123'),
            firstName: 'Group',
            birthDate: new \DateTimeImmutable('1990-01-01'),
            biologicalSex: BiologicalSex::MALE,
        );

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $this->token = $tokenManager->create(new SymfonyUser($this->user));
    }

    private function truncateTables(): void
    {
        $connection = $this->entityManager?->getConnection();
        if (null === $connection) {
            return;
        }

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('DELETE FROM glorious_magra_activities');
        $connection->executeStatement('DELETE FROM glorious_magra_group_memberships');
        $connection->executeStatement('DELETE FROM glorious_magra_community_groups');
        $connection->executeStatement('DELETE FROM glorious_magra_measurements');
        $connection->executeStatement('DELETE FROM glorious_magra_users');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function decodeResponse(): array
    {
        $content = $this->client->getResponse()->getContent();

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
