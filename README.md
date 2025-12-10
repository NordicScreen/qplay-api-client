# Q-Play API Client

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

A lightweight PHP client for the NordicScreen Q-Play REST API.

This library provides a convenient PHP interface for authenticating with Q-Play using OAuth2 Client Credentials and calling the various API sections (Media, Players, Presentations, Integrations, etc.).

---

## Requirements

- PHP 8.3 or higher
- guzzlehttp/guzzle ^7.0
- psr/cache ^3.0 (optional, for token caching)

The package uses PSR-4 autoloading under the namespace `QPlay\Api\Client`.

## Installation

Install via Composer:

```
composer require nordicscreen/q-play-api-client
```

## Getting Started

You will need the following from your Q-Play environment:

- API base URL (REST endpoint), for example: `https://your-qplay-host.tld/api/rest`
- OAuth2 Client Credentials: `client_id` and `client_secret`
- Optional: Scopes required for your use case (ask your Q-Play administrator if unsure)

### Basic usage

```php
<?php

use QPlay\Api\Client\QPlayClient;

$api = new QPlayClient(
    apiEndpoint: 'https://your-qplay-host.tld/api/rest',
    clientId:    'your-client-id',
    clientSecret:'your-client-secret',
    scopes:      ['media:read', 'media:write'] // optional; provide only the scopes you need
);

// Example: list media items
$mediaList = $api->media()->list(['page' => 1, 'per_page' => 25]);

// Example: get a single media item
$media = $api->media()->getById(123);

// Example: create/update/delete (payload depends on your Q-Play API contract)
//$created = $api->media()->create(['name' => 'My file', ...]);
//$updated = $api->media()->update(123, ['name' => 'Renamed']);
//$api->media()->deleteById(123);
```

Notes:
- Most API methods return `array` when the response is JSON, otherwise `string` (e.g., for binary/stream responses). Handle both accordingly.
- Section helpers expose common operations; for advanced endpoints you can still use the generic section methods like `get`, `post`, `put`, `patch`, and `delete`.

### Token caching (optional but recommended)

You can provide a PSR-6 `CacheItemInterface` to persist the OAuth2 access token between requests/processes. This minimizes token requests and improves performance.

```php
<?php

use QPlay\Api\Client\QPlayClient;
use Symfony\Component\Cache\Adapter\FilesystemAdapter; // or any PSR-6 cache

$cache = new FilesystemAdapter(namespace: 'qplay', defaultLifetime: 0);
$tokenItem = $cache->getItem('oauth_access_token');

$api = new QPlayClient(
    apiEndpoint: 'https://your-qplay-host.tld/api/rest',
    clientId:    'your-client-id',
    clientSecret:'your-client-secret',
    scopes:      ['media:read'],
    tokenCacheItem: $tokenItem
);
```

The client will:
- Read the token payload from the cache item if present and valid
- Store/update the token and its expiration when a new token is obtained
- Clear the cached token if it becomes invalid

### Custom HTTP client (advanced)

If you need custom timeouts, proxies, logging, or TLS options, you can pass in your own `GuzzleHttp\Client` instance:

```php
<?php

use GuzzleHttp\Client as GuzzleClient;
use QPlay\Api\Client\QPlayClient;

$guzzle = new GuzzleClient([
    'timeout' => 10.0,
    // 'proxy' => 'http://proxy:8080',
    // 'verify' => '/path/to/ca.pem',
]);

$api = new QPlayClient(
    apiEndpoint: 'https://your-qplay-host.tld/api/rest',
    clientId:    'your-client-id',
    clientSecret:'your-client-secret',
    scopes:      [],
    tokenCacheItem: null,
    httpClient:  $guzzle
);
```

## API Sections overview

The high-level `QPlayClient` provides typed accessors for the different API sections:

- `media()` — Media Library API (`/media`)
- `players()` — Players API (`/players`)
- `presentations()` — Presentations API (`/presentations`)
- `events()` — Events API (`/events`)
- `integration()` — Integration API (`/integration`)
- `integrationTemplate()` — Integration Template API (`/integration-template`)
- `app()` — App section (read-only)
- `appTemplate()` — App Template section (read-only)
- `system()` — System API (`/system`)
- `server()` — Server API (`/server`)
- `developerApp()` — Developer Apps (`/developer/apps`)
- `developerAppTemplate()` — Developer App Templates (`/developer/app-template`)
- `developerIntegrationTemplate()` — Developer Integration Templates (`/developer/integration-template`)

Each section implements convenience methods where available. For endpoints that are not covered by a helper method, you can use the generic methods:

```php
$api->media()->get('custom-path', ['foo' => 'bar']);
$api->media()->post('custom-path', ['json' => ['key' => 'value']]);
```

## Error handling

All HTTP and validation errors are converted to domain exceptions. You can catch the base `QPlay\Api\Client\Exception\ApiException` or handle specific cases:

- `ValidationException`
- `UnauthorizedException`
- `ForbiddenException`
- `NotFoundException`
- `MethodNotAllowedException`
- `TooManyRequestsException`
- `ServerErrorException`

Example:

```php
use QPlay\Api\Client\Exception\ApiException;

try {
    $media = $api->media()->getById(123);
} catch (ApiException $e) {
    // Inspect $e->getMessage(), $e->getCode(), or response details if exposed
}
```

## Tips

- Scopes: provide only the scopes you actually need; if omitted, the client requests without any scope parameter.
- Base URL: always use the REST base (no trailing slash), e.g., `https://your-qplay-host.tld/api/rest`.
- Responses: some endpoints may return non-JSON (e.g., downloads). Treat responses as `string|array`.

## License

This project is released under the MIT License. See [`LICENSE`](LICENSE) for details.
