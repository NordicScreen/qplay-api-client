# Q-Play API Client

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

A lightweight, framework-agnostic PHP client for the [NordicScreen Q-Play](https://nordicscreen.com) REST API.

This library provides a typed, section-based interface for authenticating with Q-Play via OAuth2 Client Credentials and calling all API domains — Media, Players, Presentations, Integrations, Apps, and more.

## ✨ Features

- **OAuth2 Client Credentials** authentication with automatic token refresh
- **Optional token caching** via PSR-6 (`psr/cache`) for cross-request persistence
- **Section-based API** — typed accessors for each API domain (`media()`, `players()`, etc.)
- **Typed exception hierarchy** — HTTP status codes mapped to specific exceptions
- **Framework-agnostic** — works in any PHP 8.3+ project (Laravel, Symfony, plain PHP, etc.)
- **Custom HTTP client** support — inject your own Guzzle configuration

## 📋 Requirements

| Dependency | Version |
|---|---|
| PHP | `>= 8.3` |
| GuzzleHTTP | `^7.0` |
| PSR-6 Cache | `^3.0` (optional, for token caching) |

The package uses PSR-4 autoloading under the namespace `QPlay\Api\Client`.

## 📦 Installation

```bash
composer require nordicscreen/q-play-api-client
```

## 🚀 Quick Start

### 1. Set up credentials

You will need the following from your Q-Play environment:

- **API base URL** (REST endpoint), e.g. `https://api.q-play.io/api/rest/v2` (production)
- **OAuth2 Client ID** and **Client Secret**
- **Scopes** (optional) — ask your Q-Play administrator if unsure

### 2. Create the client and make requests

```php
<?php

use QPlay\Api\Client\QPlayClient;

$api = new QPlayClient(
    apiEndpoint:  'https://api.q-play.io/api/rest/v2',
    clientId:     'your-client-id',
    clientSecret: 'your-client-secret',
    scopes:       ['media:read', 'media:write']
);

// List media files
$files = $api->media()->listFiles(['page' => 1, 'per_page' => 25]);

// Get a single media file
$file = $api->media()->getFile(123);

// Check system health
$health = $api->system()->health();
```

## 📚 Documentation

Detailed documentation is available in the [`docs/`](docs/) folder:

| Topic | Description |
|---|---|
| [Authentication](docs/authentication.md) | OAuth2 flow, token caching, custom HTTP client |
| [API Reference](docs/api-reference.md) | Complete method reference for all API sections |
| [Error Handling](docs/error-handling.md) | Exception hierarchy and usage patterns |
| [Architecture](docs/architecture.md) | Internal design, section pattern, extension points |

For the full API specification, see the [Official Q-Play REST API Documentation](https://developer.q-play.io/docs/api-docs/qplay-rest).

## 🧪 Running Tests

```bash
composer test
# or
./vendor/bin/phpunit
```

## 📄 License

This project is released under the MIT License. See [`LICENSE`](LICENSE) for details.
