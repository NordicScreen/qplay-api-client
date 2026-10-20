# Architecture

This document describes the internal architecture of the Q-Play API Client, including design patterns, the section-based model, and extension points.

> **Official API Documentation**: [Q-Play REST API Docs](https://developer.q-play.io/docs/api-docs/qplay-rest)
> **Production Base URL**: `https://api.q-play.io/api/rest/v2`

## Overview

```
qplay-api-client/
├── src/
│   ├── Client.php                 # Core: OAuth2, token caching, HTTP requests
│   ├── QPlayClient.php            # Facade: typed section accessors
│   ├── Exception/                 # Typed exception hierarchy
│   │   ├── ApiException.php       # Base exception
│   │   ├── BadRequestException.php
│   │   ├── UnauthorizedException.php
│   │   ├── ForbiddenException.php
│   │   ├── NotFoundException.php
│   │   ├── MethodNotAllowedException.php
│   │   ├── ValidationException.php
│   │   ├── TooManyRequestsException.php
│   │   └── ServerErrorException.php
│   └── Sections/                  # API section classes
│       ├── AbstractSection.php    # Base class with generic HTTP methods
│       ├── MediaLibrarySection.php
│       ├── PlayersSection.php
│       ├── PresentationsSection.php
│       ├── PlaylistSection.php
│       ├── IntegrationSection.php
│       ├── IntegrationTemplateSection.php
│       ├── AppSection.php
│       ├── AppTemplateSection.php
│       ├── SystemSection.php
│       └── Developer/             # Developer API sub-sections
│           ├── AppSection.php
│           ├── AppTemplateSection.php
│           ├── IntegrationTemplateSection.php
│           ├── AppMediaSection.php
│           └── AppTemplateMediaSection.php
└── tests/
```

## Core Components

### 1. `Client` (Core)

The `Client` class is the heart of the library. It handles:

- **OAuth2 authentication** — Obtains and manages access tokens via the Client Credentials grant.
- **Token caching** — Integrates with PSR-6 `CacheItemInterface` for cross-request token persistence.
- **HTTP requests** — Delegates to Guzzle with automatic bearer token injection.
- **Exception mapping** — Converts HTTP status codes to typed exceptions.
- **Lazy section loading** — Section instances are created on first access and reused.

The `Client` exposes raw HTTP methods (`get`, `post`, `put`, `patch`, `delete`, `request`) for direct API access.

### 2. `QPlayClient` (Facade)

`QPlayClient` is a thin facade over `Client` that provides:

- **Friendly constructor** — Named parameters for all configuration options.
- **Typed accessors** — Each section accessor returns the correct section type (e.g., `media()` returns `MediaLibrarySection`).
- **Core access** — `core()` returns the underlying `Client` for advanced use.

```php
// QPlayClient is the recommended entry point
$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'id',
    clientSecret: 'secret'
);

// Access the core client if needed
$core = $api->core();
```

### 3. `AbstractSection` (Base)

All section classes extend `AbstractSection`, which provides:

- **Path composition** — The `path()` method builds section-relative paths.
- **Generic HTTP methods** — `get()`, `post()`, `put()`, `patch()`, `delete()`, and `request()` with automatic path prefixing.
- **Client reference** — Protected `$client` property for delegating requests.

```php
abstract class AbstractSection
{
    protected Client $client;
    protected string $basePath;

    public function __construct(Client $client, string $basePath) { ... }
    protected function path(string $path = ''): string { ... }
    public function get(string $path = '', array $query = [], array $headers = []): string|array { ... }
    public function post(string $path = '', array $body = [], array $headers = []): string|array { ... }
    // ... etc
}
```

### 4. Domain Sections

Each API domain (Media, Players, Presentations, etc.) extends `AbstractSection` and adds typed convenience methods. For example, `MediaLibrarySection` provides `listFolders()`, `getFolder()`, `createFile()`, etc.

### 5. Exception Hierarchy

All exceptions extend `ApiException`, which extends `RuntimeException`. Each HTTP status code maps to a specific exception class, enabling precise error handling.

## Section-Based API Pattern

The library uses a **section-based pattern** where each API domain is a separate class:

```
QPlayClient (facade)
    └── Client (core)
            ├── media()        → MediaLibrarySection
            ├── players()      → PlayersSection
            ├── presentations() → PresentationsSection
            ├── playlist()     → PlaylistSection
            ├── integration()  → IntegrationSection
            ├── system()       → SystemSection
            └── ...
```

### Benefits

1. **Type safety** — Each section returns a typed object, enabling IDE autocompletion.
2. **Separation of concerns** — Each section class focuses on one API domain.
3. **Extensibility** — New sections can be added without modifying existing code.
4. **Lazy loading** — Sections are instantiated only when first accessed.

## Request Flow

```
User calls $api->media()->getFile(123)
    │
    ▼
QPlayClient::media() returns MediaLibrarySection (lazy-loaded)
    │
    ▼
MediaLibrarySection::getFile(123) calls $this->get('file/123')
    │
    ▼
AbstractSection::get() delegates to Client::get('media/file/123')
    │
    ▼
Client::get() calls Client::request('GET', 'media/file/123')
    │
    ▼
Client::request() obtains OAuth2 token (if needed)
    │
    ▼
Client::request() sends HTTP request via Guzzle with Bearer token
    │
    ▼
Response is decoded (JSON → array, or raw string)
    │
    ▼
Result is returned to the user
```

## Token Management

The client manages OAuth2 tokens with this flow:

1. **Check memory** — If a valid token exists in memory (not within 30s of expiry), use it.
2. **Check cache** — If a PSR-6 cache item is provided and contains a valid token, use it.
3. **Request new token** — POST to `/oauth/token` with client credentials.
4. **Store token** — Save in memory and cache (if available).
5. **401 retry** — On `401` response, clear token and retry once.

## Response Handling

All API methods return `string|array`:

- **JSON responses** are decoded to `array`.
- **Non-JSON responses** (binary downloads, empty bodies) return `string`.

```php
$response = $api->media()->getFile(123);

if (is_array($response)) {
    // JSON response
    echo $response['name'];
} else {
    // String response (e.g., binary data)
    file_put_contents('download.bin', $response);
}
```

## Extension Points

### Adding a New Section

To add a new API section:

1. **Create the section class** extending `AbstractSection`:

```php
<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

class MyNewSection extends AbstractSection
{
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }
}
```

2. **Register in `Client`**:

```php
private ?MyNewSection $myNewSection = null;

public function myNewSection(): MyNewSection
{
    return $this->myNewSection ??= new MyNewSection($this, 'my-new-section');
}
```

3. **Add accessor to `QPlayClient`**:

```php
public function myNewSection(): MyNewSection
{
    return $this->core->myNewSection();
}
```

### Custom HTTP Middleware

Inject a custom Guzzle client with middleware:

```php
<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

$history = [];
$stack = HandlerStack::create();
$stack->push(Middleware::history($history));

$guzzle = new GuzzleClient([
    'handler' => $stack,
    'timeout' => 30.0,
]);

$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'id',
    clientSecret: 'secret',
    httpClient:   $guzzle
);
```

### Custom Token Cache

Any PSR-6 compatible cache works:

```php
<?php

use Symfony\Component\Cache\Adapter\RedisAdapter;

$cache = new RedisAdapter(
    RedisAdapter::createConnection('redis://localhost:6379'),
    namespace: 'qplay',
    defaultLifetime: 0
);
$tokenItem = $cache->getItem('oauth_access_token');

$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'id',
    clientSecret: 'secret',
    tokenCacheItem: $tokenItem
);
```

## Design Decisions

### Why `string|array` return types?

The Q-Play API returns JSON for most endpoints, but some endpoints (file downloads, binary data) return non-JSON responses. The union type `string|array` covers both cases without requiring the caller to know the response type in advance.

### Why lazy section loading?

Sections are created on first access to avoid unnecessary object instantiation. If a user only calls `media()`, the `PlayersSection` is never created.

### Why `QPlayClient` facade?

The facade provides a cleaner public API with named parameters and typed return types, while keeping the internal `Client` class focused on HTTP and authentication logic.

### Why `int|string` for IDs?

API IDs may be integers or UUIDs depending on the endpoint. Accepting both types avoids the need for manual casting at every call site.
