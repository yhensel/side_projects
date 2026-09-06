<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Domain\Measurement\Entity\Measurement;
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

class MeasurementControllerTest extends WebTestCase
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

    public function testItCreatesMeasurementViaApi(): void
    {
        $payload = [
            'date' => '2026-07-20',
            'weight' => 82.5,
            'height' => 180.0,
            'neck' => 38.0,
            'waist' => 90.0,
        ];

        $this->client->request(
            'POST',
            '/api/measurements',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
            content: json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseData = $this->decodeResponse();
        $this->assertTrue($responseData['success']);
        $this->assertSame('2026-07-20', $responseData['data']['date']);
        $this->assertSame(82.5, $responseData['data']['weight']);
        $this->assertNotNull($responseData['data']['id']);
        $this->assertIsNumeric($responseData['data']['calculatedFatPercentage']);
        $this->assertGreaterThan(0, $responseData['data']['calculatedFatPercentage']);

        $measurements = $this->entityManager->getRepository(Measurement::class)->findBy(['userId' => $this->user->getId()]);
        $this->assertCount(1, $measurements);
    }

    public function testItReturnsValidationErrorsForInvalidMeasurementPayload(): void
    {
        $payload = [
            'date' => '2026-07-20',
            'weight' => 'invalid',
            'height' => 180.0,
            'neck' => 38.0,
            'waist' => 90.0,
        ];

        $this->client->request(
            'POST',
            '/api/measurements',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
            content: json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseData = $this->decodeResponse();
        $this->assertIsArray($responseData);
        $this->assertContains('weight', array_column($responseData, 'property'));
    }

    public function testItListsAndShowsMeasurementDetailsForAuthenticatedUser(): void
    {
        $payload = [
            'date' => '2026-07-20',
            'weight' => 82.5,
            'height' => 180.0,
            'neck' => 38.0,
            'waist' => 90.0,
        ];

        $this->client->request(
            'POST',
            '/api/measurements',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
            content: json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $createdData = $this->decodeResponse();
        $measurementId = $createdData['data']['id'];

        $this->client->request(
            'GET',
            '/api/measurements',
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $listData = $this->decodeResponse();
        $this->assertCount(1, $listData['data']);
        $this->assertSame($measurementId, $listData['data'][0]['id']);

        $this->client->request(
            'GET',
            '/api/measurements/'.$measurementId,
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token,
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $detailData = $this->decodeResponse();
        $this->assertSame($measurementId, $detailData['data']['id']);
        $this->assertSame('2026-07-20', $detailData['data']['date']);
    }

    private function createAuthenticatedUser(): void
    {
        $passwordHasher = static::getContainer()->get(PasswordHasher::class);
        $tokenManager = static::getContainer()->get(JWTTokenManagerInterface::class);

        $this->user = User::register(
            id: Uuid::v4()->toString(),
            email: Email::fromString('measurement-'.uniqid('', true).'@example.com'),
            passwordHash: $passwordHasher->hash('secret123'),
            firstName: 'Measurement',
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
