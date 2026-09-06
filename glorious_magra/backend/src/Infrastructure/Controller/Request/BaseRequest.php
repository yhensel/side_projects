<?php

declare(strict_types=1);


namespace App\Infrastructure\Controller\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Infrastructure\Controller\Request\Exception\RequestValidationException;


abstract class BaseRequest extends Request
{
    /**
     * Validate the class instance.
     *
     *
     * @throws RequestValidationException
     */
    public function validate(ValidatorInterface $validator): void
    {
        /* General method to modify values before validation (Can be overridden) */
        $this->prepareValidation();

        $violations = $validator->validate($this->getAllParams(), $this->getRules());

        if (!$this->passesValidation($violations)) {
            $this->failedValidation($violations);
        }
        /* General method to modify values after validation (Can be overridden) */
        $this->afterValidation();
    }

    protected function getAllParams(): array
    {
        return array_merge($this->request->all(), $this->query->all());
    }

    public function getParam($value): mixed
    {
        return $this->request->get($value);
    }

    public function getQuery($value): mixed
    {
        return $this->query->get($value);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareValidation(): void
    {
        /* Merge raw body into the request object */
        if ($raw = $this->getRawContent()) {
            $this->request->add($raw);
        }
    }

    private function getRawContent(): ?array
    {
        return ($raw = $this->getContent()) ? json_decode($raw, true) : [];
    }

    protected function afterValidation(): void
    {
    }

    /**
     * Check for validation fail.
     */
    protected function passesValidation(ConstraintViolationListInterface $violations): bool
    {
        return 0 == $violations->count();
    }

    /**
     * Behaviour if the Request validation fails.
     * @throws RequestValidationException
     */
    protected function failedValidation(ConstraintViolationListInterface $violations): void
    {
        /* 422 Error -> Unprocessable entity */
        throw new RequestValidationException($violations);
    }

    /**
     * Set custom rules for request.
     */
    public function getRules(): array
    {
        return [];
    }

    /**
     * This method returns an array with ONLY the indexes present in the body
     * This is useful for PATCH operations where you don't want to get "false nulls" from the request validation
     */
    public function getAllParamsInBody(): array
    {
        return $this->request->all();
    }
}