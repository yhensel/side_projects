<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\Measurement\Exception\MeasurementNotFoundException;
use App\Infrastructure\Controller\Request\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Listener to capture all kernel exceptions and transform the response to a JsonResponse
 *
 * @package App\Infrastructure\EventListener
 */
class ApiExceptionListener
{
    private readonly bool $stackTraceOnError;
    private readonly string $environment;

    public function __construct(bool $stackTraceOnError, string $environment)
    {
        $this->stackTraceOnError = $stackTraceOnError;
        $this->environment = $environment;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $this->unwrapException($event->getThrowable());

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $responsePayload = [
            'success' => false,
            'error' => 'Internal server error',
        ];

        if ($exception instanceof RequestValidationException) {
            $event->setResponse(new JsonResponse($exception->getErrors(), $exception->getCode()));

            return;
        }

        if ($exception instanceof UserAlreadyExistsException) {
            $statusCode = Response::HTTP_CONFLICT;
            $responsePayload['error'] = $exception->getMessage();
        }

        if ($exception instanceof AuthenticationException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $responsePayload['error'] = 'Invalid credentials';
        }

        if ($exception instanceof AccessDeniedException) {
            $statusCode = Response::HTTP_FORBIDDEN;
            $responsePayload['error'] = $exception->getMessage();
        }

        if ($exception instanceof MeasurementNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $responsePayload['error'] = $exception->getMessage();
        }

        if ($exception instanceof \InvalidArgumentException) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            $responsePayload['error'] = $exception->getMessage();
        }

        if ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR && $this->stackTraceOnError) {
            $responsePayload['exception'] = $exception::class;
            $responsePayload['message'] = $exception->getMessage();
            $responsePayload['trace'] = $exception->getTrace();
        }

        $event->setResponse(new JsonResponse($responsePayload, $statusCode));
    }

    private function unwrapException(\Throwable $exception): \Throwable
    {
        if (!$exception instanceof HandlerFailedException) {
            return $exception;
        }

        return $exception->getPrevious() ?? $exception;
    }
}
