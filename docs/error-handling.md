# Error Handling

All API errors are converted to typed PHP exceptions that extend the base `ApiException`. This allows you to catch errors at different levels of specificity.

> **Official API Documentation**: [Q-Play REST API Docs](https://developer.q-play.io/docs/api-docs/qplay-rest)
> **Production Base URL**: `https://api.q-play.io/api/rest/v2`

## Exception Hierarchy

```
RuntimeException
└── ApiException (base class)
    ├── BadRequestException       (HTTP 400)
    ├── UnauthorizedException     (HTTP 401)
    ├── ForbiddenException        (HTTP 403)
    ├── NotFoundException         (HTTP 404)
    ├── MethodNotAllowedException (HTTP 405)
    ├── ValidationException       (HTTP 422)
    ├── TooManyRequestsException  (HTTP 429)
    └── ServerErrorException      (HTTP 500–504)
```

## ApiException Base Class

All exceptions extend `QPlay\Api\Client\Exception\ApiException`, which provides:

| Method | Return Type | Description |
|---|---|---|
| `getMessage()` | `string` | Error message from the API |
| `getCode()` | `int` | HTTP status code |
| `getStatusCode()` | `int` | HTTP status code (alias) |
| `getDetails()` | `array` | Additional error details (e.g., validation errors) |
| `getPrevious()` | `?Throwable` | Underlying exception, if any |

## Catching Errors

### Catch All API Errors

```php
<?php

use QPlay\Api\Client\Exception\ApiException;

try {
    $media = $api->media()->getFile(123);
} catch (ApiException $e) {
    echo "API Error ({$e->getCode()}): {$e->getMessage()}";
}
```

### Catch Specific Errors

```php
<?php

use QPlay\Api\Client\Exception\NotFoundException;
use QPlay\Api\Client\Exception\ValidationException;
use QPlay\Api\Client\Exception\TooManyRequestsException;

try {
    $media = $api->media()->createFile(['name' => '']);
} catch (NotFoundException $e) {
    // Resource not found
    echo "Not found: {$e->getMessage()}";
} catch (ValidationException $e) {
    // Validation failed — check details
    echo "Validation error: {$e->getMessage()}";
    print_r($e->getDetails());
} catch (TooManyRequestsException $e) {
    // Rate limited — back off and retry
    echo "Rate limited. Try again later.";
}
```

## HTTP Status Code Mapping

| HTTP Status | Exception | Typical Cause |
|---|---|---|
| `400` | `BadRequestException` | Malformed request |
| `401` | `UnauthorizedException` | Invalid or expired token |
| `403` | `ForbiddenException` | Insufficient permissions/scopes |
| `404` | `NotFoundException` | Resource does not exist |
| `405` | `MethodNotAllowedException` | HTTP method not allowed for endpoint |
| `422` | `ValidationException` | Request body validation failed |
| `429` | `TooManyRequestsException` | Rate limit exceeded |
| `500–504` | `ServerErrorException` | Server-side error |

## Validation Errors

When the API returns a `422 Unprocessable Entity`, the `ValidationException` includes detailed field-level errors in `getDetails()`:

```php
<?php

use QPlay\Api\Client\Exception\ValidationException;

try {
    $api->media()->createFolder(['name' => '']);
} catch (ValidationException $e) {
    echo $e->getMessage(); // "Validation failed"
    
    $details = $e->getDetails();
    // [
    //     'name' => ['The name field is required.'],
    //     'parent_id' => ['The parent id must be an integer.'],
    // ]
}
```

## Automatic 401 Retry

The client automatically handles expired tokens:

1. If a request receives a `401 Unauthorized` response, the client clears the current token.
2. The client retries the request once with a freshly obtained token.
3. If the retry also returns `401`, an `UnauthorizedException` is thrown.

This means you rarely need to handle token expiration manually — the client manages it transparently.

## Transport Errors

Network-level errors (DNS failures, connection timeouts, SSL errors) are wrapped in `ServerErrorException`:

```php
<?php

use QPlay\Api\Client\Exception\ServerErrorException;

try {
    $health = $api->system()->health();
} catch (ServerErrorException $e) {
    // Could be a server error (5xx) or a transport error
    if ($e->getPrevious()) {
        // Transport error
        echo "Connection failed: {$e->getMessage()}";
    } else {
        // Server error
        echo "Server error: {$e->getMessage()}";
    }
}
```

## Best Practices

1. **Catch specific exceptions first** — Handle `ValidationException`, `NotFoundException`, etc. before the base `ApiException`.
2. **Check `getDetails()` for validation errors** — Field-level error messages help users correct their input.
3. **Implement retry logic for `TooManyRequestsException`** — Use exponential backoff for rate-limited requests.
4. **Log `ServerErrorException` with `$e->getPrevious()`** — Transport errors contain the underlying Guzzle exception with more context.
5. **Don't swallow `UnauthorizedException`** — If the retry fails, credentials are likely invalid; log and alert.
