<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Health;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController
{
    public function __construct(private Connection $connection)
    {}

    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $checks = [
            'api' => $this->checkApi(),
            'database' => $this->checkDatabase(),
        ];

        $status = $this->overallStatus($checks);

        return new JsonResponse([
            'status' => $status,
            'checks' => $checks,
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ], $status === 'ok' ? 200 : 503);
    }

    /**
     * Checks if the API is running.
     */
    private function checkApi(): array
    {
        return [
            'status' => 'ok',
            'message' => 'Symfony is running',
        ];
    }

    /**
     * Checks the database connection.
     */
    private function checkDatabase(): array
    {
        try {
            // Lightweight query to validate real connection
            $this->connection->executeQuery('SELECT 1');

            return [
                'status' => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed',
            ];
        }
    }

    /**
     * Determines the overall status based on individual checks.
     */
    private function overallStatus(array $checks): string
    {
        foreach ($checks as $check) {
            if ($check['status'] !== 'ok') {
                return 'error';
            }
        }

        return 'ok';
    }
}
