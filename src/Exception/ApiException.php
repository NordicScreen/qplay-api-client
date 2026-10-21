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

    /**
     * Construct a new API exception.
     *
     * @param string $message The exception message
     * @param int $statusCode The HTTP status code
     * @param array $details Additional error details (e.g., validation errors)
     * @param Throwable|null $previous The previous exception for chaining
     */
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
     *
     * @return int The HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Returns additional error information (e.g., validation errors)
     *
     * @return array The error details
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
