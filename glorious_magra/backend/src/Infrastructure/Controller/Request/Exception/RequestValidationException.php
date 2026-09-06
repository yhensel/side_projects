<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Request\Exception;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\Response;

final class RequestValidationException extends Exception
{
    private const string CONSTRAIN_INVALID_PARAM_TYPE = 'invalid_parameter';
    private const string CONSTRAIN_INVALID_FORMAT_TYPE = 'invalid_format';
    private const string CONSTRAIN_INVALID_LENGTH_TYPE = 'invalid_length';
    private const string CONSTRAIN_MISSING_PARAM_TYPE = 'missing_parameter';
    private const string ALIAS_422 = 'validation_error';
    private const string GENERIC_422_MESSAGE = 'Unprocessable validation.';
    protected string $errorType;

    protected array $constrainTypes = [
        Type::class => self::CONSTRAIN_INVALID_PARAM_TYPE,
        Regex::class => self::CONSTRAIN_INVALID_FORMAT_TYPE,
        DateTime::class => self::CONSTRAIN_INVALID_FORMAT_TYPE,
        Length::class => self::CONSTRAIN_INVALID_LENGTH_TYPE,
        Collection::class => self::CONSTRAIN_MISSING_PARAM_TYPE,
        Required::class => self::CONSTRAIN_MISSING_PARAM_TYPE,
        NotBlank::class => self::CONSTRAIN_MISSING_PARAM_TYPE,
    ];

    private readonly array $errors;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct(self::GENERIC_422_MESSAGE, Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->errors = $this->formatValidationErrors($violations);
    }

    /**
     * Format the errors from the given Validator instance.
     *
     *
     */
    protected function formatValidationErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            /** @var ConstraintViolation $violation */
            $errors[] = [
                'type' => $this->getType($violation->getConstraint()),
                'property' => $this->cleanProperty($violation->getPropertyPath()),
                'details' => $violation->getMessage(),
            ];
        }
        
        return $errors;
    }

    /**
     * @return string|string[]
     */
    private function cleanProperty($property): array|string
    {
        $parts = explode(']', (string) $property);
        array_pop($parts);

        if (count($parts) == 1) {
            return str_replace('[', '', str_replace(']', '', $property));
        }

        return $property;
    }

    private function getType(Constraint $constraint): string
    {
        return $this->constrainTypes[get_class($constraint)] ?? self::ALIAS_422;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}