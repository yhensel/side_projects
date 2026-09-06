<?php

declare(strict_types=1);


namespace App\Infrastructure\Controller\Request\Resolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use App\Infrastructure\Controller\Request\BaseRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Infrastructure\Controller\Request\Exception\RequestValidationException;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class RequestResolver implements ValueResolverInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @throws RequestValidationException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (Request::class != $argument->getType() && !is_subclass_of($argument->getType(), Request::class)) {
            return [];
        }

        /* We just take into consideration for validation BaseRequests instances */
        if (is_subclass_of($argument->getType(), BaseRequest::class)) {
            /* Create custom Request from main one without recreating from globals */
            $desiredRequestClass = $argument->getType();
            $customRequest = new $desiredRequestClass();
            $customRequest->initialize(
                $request->query->all(),
                $request->request->all(),
                $request->attributes->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                $request->getContent(),
            );
            /* Validate Request inside the custom class */
            $customRequest->validate($this->validator);
            yield $customRequest;
        } else {
            yield $request;
        }
    }
}