<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Exception;

use RuntimeException;
use Throwable;

/**
 * Base exception for all API errors.
 *
 * @namespace QPlay\Api\Client\Exception
 */
class ApiException extends RuntimeException
{
    /** @var int */
    protected int $statusCode;

    /** @var array */
    protected array $details;

    public function __construct(
        string $message = "API Error",
        int $statusCode = 0,
        array $details = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    /**
     * Get HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Returns additional error information (e.g., validation errors)
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
