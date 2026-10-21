# Authentication

The Q-Play API Client uses **OAuth2 Client Credentials** grant flow for authentication. The client handles token acquisition, caching, and automatic refresh transparently.

> **Official API Documentation**: [Q-Play REST API Docs](https://developer.q-play.io/docs/api-docs/qplay-rest)
> **Production Base URL**: `https://api.q-play.io/api/rest/v2`

## Basic Authentication

When you create a `QPlayClient`, you provide your OAuth2 credentials. The client automatically obtains an access token on the first API call and reuses it until it expires.

```php
<?php

use QPlay\Api\Client\QPlayClient;

$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'your-client-id',
    clientSecret: 'your-client-secret',
    scopes:       ['media:read', 'media:write']
);
```

### Constructor Parameters

| Parameter | Type | Required | Description |
|---|---|---|---|
| `apiEndpoint` | `string` | Yes | Base REST API URL (no trailing slash) |
| `clientId` | `string` | Yes | OAuth2 client ID |
| `clientSecret` | `string` | Yes | OAuth2 client secret |
| `scopes` | `array` | No | OAuth2 scopes (space-separated internally) |
| `tokenCacheItem` | `?CacheItemInterface` | No | PSR-6 cache item for token persistence |
| `httpClient` | `?GuzzleHttpClient` | No | Custom Guzzle HTTP client instance |

## Token Lifecycle

The client manages the OAuth2 token automatically:

1. **First request** — The client requests a new token from `/oauth/token` using `client_credentials` grant.
2. **Token storage** — The token and expiration time are stored in memory. If a cache item is provided, the token is also persisted.
3. **Token reuse** — Subsequent requests reuse the cached token until it is within 30 seconds of expiration.
4. **Token refresh** — When the token is near expiration, a new token is obtained automatically.
5. **401 retry** — If a request receives a `401 Unauthorized` response, the client clears the token and retries once with a fresh token.

## Token Caching

For long-running processes or CLI scripts, you can provide a PSR-6 `CacheItemInterface` to persist the OAuth2 token between requests. This avoids unnecessary token requests and improves performance.

```php
<?php

use QPlay\Api\Client\QPlayClient;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$cache = new FilesystemAdapter(
    namespace: 'qplay',
    defaultLifetime: 0
);
$tokenItem = $cache->getItem('oauth_access_token');

$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'your-client-id',
    clientSecret: 'your-client-secret',
    scopes:       ['media:read'],
    tokenCacheItem: $tokenItem
);
```

The client will:

- **Read** the token from the cache item if present and still valid
- **Store** the token and expiration when a new token is obtained
- **Clear** the cached token if it becomes invalid (401 response)

### Cache Implementation Options

Any PSR-6 compatible cache adapter works:

- **Filesystem**: `Symfony\Component\Cache\Adapter\FilesystemAdapter`
- **APCu**: `Symfony\Component\Cache\Adapter\ApcuAdapter`
- **Redis**: `Symfony\Component\Cache\Adapter\RedisAdapter`
- **Memcached**: `Symfony\Component\Cache\Adapter\MemcachedAdapter`

## Scopes

Scopes control which API sections your client can access. Provide only the scopes you need:

```php
// Read-only media access
$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'your-client-id',
    clientSecret: 'your-client-secret',
    scopes:       ['media:read']
);

// Multiple scopes
$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'your-client-id',
    clientSecret: 'your-client-secret',
    scopes:       ['media:read', 'media:write', 'players:read']
);

// No scopes (if your API doesn't require them)
$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'your-client-id',
    clientSecret: 'your-client-secret'
);
```

If scopes are omitted or an empty array is passed, the token request is sent without a `scope` parameter.

## Custom HTTP Client

For advanced use cases — custom timeouts, proxies, logging middleware, or TLS configuration — you can inject your own Guzzle client:

```php
<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use QPlay\Api\Client\QPlayClient;

// Create a history container for logging
$history = Middleware::history($container = new \SplQueue());

// Build handler stack with middleware
$stack = HandlerStack::create();
$stack->push($history);

$guzzle = new GuzzleClient([
    'timeout' => 30.0,
    'connect_timeout' => 10.0,
    'handler' => $stack,
    // 'proxy' => 'http://proxy:8080',
    // 'verify' => '/path/to/ca.pem',
]);

$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'your-client-id',
    clientSecret: 'your-client-secret',
    httpClient:   $guzzle
);
```

## Core Client Access

If you need direct access to the underlying `Client` (for raw HTTP requests or advanced operations):

```php
<?php

$core = $api->core();

// Direct HTTP methods
$response = $core->get('custom/endpoint', ['query' => 'param']);
$response = $core->post('custom/endpoint', ['key' => 'value']);
$response = $core->put('custom/endpoint', ['key' => 'value']);
$response = $core->patch('custom/endpoint', ['key' => 'value']);
$response = $core->delete('custom/endpoint');
```
