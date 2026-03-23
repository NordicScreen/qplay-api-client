<?php

declare(strict_types=1);

namespace QPlay\Api\Client;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Cache\CacheItemInterface;
use QPlay\Api\Client\Exception\ApiException;
use QPlay\Api\Client\Exception\BadRequestException;
use QPlay\Api\Client\Exception\ForbiddenException;
use QPlay\Api\Client\Exception\MethodNotAllowedException;
use QPlay\Api\Client\Exception\NotFoundException;
use QPlay\Api\Client\Exception\ServerErrorException;
use QPlay\Api\Client\Exception\TooManyRequestsException;
use QPlay\Api\Client\Exception\UnauthorizedException;
use QPlay\Api\Client\Exception\ValidationException;
use QPlay\Api\Client\Sections\AppSection;
use QPlay\Api\Client\Sections\AppTemplateSection;
use QPlay\Api\Client\Sections\Developer\AppSection as DevAppSection;
use QPlay\Api\Client\Sections\Developer\AppTemplateSection as DevAppTemplateSection;
use QPlay\Api\Client\Sections\Developer\IntegrationTemplateSection as DevIntegrationTemplateSection;
use QPlay\Api\Client\Sections\EventsSection;
use QPlay\Api\Client\Sections\IntegrationSection;
use QPlay\Api\Client\Sections\IntegrationTemplateSection;
use QPlay\Api\Client\Sections\MediaLibrarySection;
use QPlay\Api\Client\Sections\PlayersSection;
use QPlay\Api\Client\Sections\PresentationsSection;
use QPlay\Api\Client\Sections\ServerSection;
use QPlay\Api\Client\Sections\SystemSection;
use Throwable;

/**
 * General REST API client for Q-Play REST API
 * - OAuth2 Client Credentials authentication
 * - Standardized response and error handling
 *
 * @namespace QPlay\Api\Client
 */
class Client
{
    private string $apiEndpoint;
    private string $clientId;
    private string $clientSecret;
    /** @var string[] */
    private array $scopes = [];

    private ?GuzzleHttpClient $httpClient = null;

    // Lazily created section instances
    private ?MediaLibrarySection $media = null;
    private ?PlayersSection $players = null;
    private ?PresentationsSection $presentations = null;
    private ?EventsSection $events = null;
    private ?IntegrationSection $integration = null;
    private ?IntegrationTemplateSection $integrationTemplate = null;
    private ?AppSection $app = null;
    private ?AppTemplateSection $appTemplate = null;
    private ?DevIntegrationTemplateSection $developerIntegrationTemplate = null;
    private ?DevAppSection $developerApp = null;
    private ?DevAppTemplateSection $developerAppTemplate = null;
    private ?SystemSection $system = null;
    private ?ServerSection $server = null;
    private ?string $token = null;
    private ?int $tokenExpiresAt = null;
    private ?CacheItemInterface $tokenCacheItem = null;

    public function __construct(
        string $apiEndpoint,
        string $clientId,
        string $clientSecret,
        array $scopes = [],
        ?CacheItemInterface $tokenCacheItem = null,
        ?GuzzleHttpClient $httpClient = null,
    ) {
        $this->apiEndpoint = rtrim($apiEndpoint, '/');
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        // normalize scopes to a list of non-empty strings
        $this->scopes = array_values(array_filter(array_map('strval', $scopes), static fn($s) => $s !== ''));
        $this->tokenCacheItem = $tokenCacheItem;
        $this->httpClient = $httpClient;
    }

    /**
     * Media Library API (/media)
     */
    public function media(): MediaLibrarySection
    {
        return $this->media ??= new MediaLibrarySection($this, 'media');
    }

    /**
     * Players API (/players)
     */
    public function players(): PlayersSection
    {
        return $this->players ??= new PlayersSection($this, 'players');
    }

    /**
     * Presentations API (/presentations)
     */
    public function presentations(): PresentationsSection
    {
        return $this->presentations ??= new PresentationsSection($this, 'presentations');
    }

    /**
     * Events API (/events)
     */
    public function events(): EventsSection
    {
        return $this->events ??= new EventsSection($this, 'events');
    }

    /**
     * Integration API (/integration)
     */
    public function integration(): IntegrationSection
    {
        return $this->integration ??= new IntegrationSection($this, 'integration');
    }

    /**
     * Integration Template API (/integration-template)
     */
    public function integrationTemplate(): IntegrationTemplateSection
    {
        return $this->integrationTemplate ??= new IntegrationTemplateSection($this, 'integration-template');
    }

    /**
     * App API (/apps) — read-only
     */
    public function app(): AppSection
    {
        return $this->app ??= new AppSection($this, 'apps');
    }

    /**
     * App Template API (/app-template) — read-only
     */
    public function appTemplate(): AppTemplateSection
    {
        return $this->appTemplate ??= new AppTemplateSection($this, 'app-template');
    }

    /**
     * System API (/system)
     */
    public function system(): SystemSection
    {
        return $this->system ??= new SystemSection($this, 'system');
    }

    /**
     * Server API (/server)
     */
    public function server(): ServerSection
    {
        return $this->server ??= new ServerSection($this, 'server');
    }

    /**
     * Developer App API (/developer/apps)
     */
    public function developerApp(): DevAppSection
    {
        return $this->developerApp
            ??= new DevAppSection($this, 'developer/apps');
    }

    /**
     * Developer App Template API (/developer/app-template)
     */
    public function developerAppTemplate(): DevAppTemplateSection
    {
        return $this->developerAppTemplate
            ??= new DevAppTemplateSection($this, 'developer/app-template');
    }

    /**
     * Developer Integration Template API (/developer/integration-template)
     */
    public function developerIntegrationTemplate(): DevIntegrationTemplateSection
    {
        return $this->developerIntegrationTemplate
            ??= new DevIntegrationTemplateSection($this, 'developer/integration-template');
    }

    public function get(string $path, array $query = [], array $headers = []): string|array
    {
        return $this->request('GET', $path, [
            'query' => $query,
            'headers' => $headers,
        ]);
    }

    public function post(string $path, array $body = [], array $headers = []): string|array
    {
        return $this->request('POST', $path, [
            'json' => $body,
            'headers' => $headers,
        ]);
    }

    public function put(string $path, array $body = [], array $headers = []): string|array
    {
        return $this->request('PUT', $path, [
            'json' => $body,
            'headers' => $headers,
        ]);
    }

    public function patch(string $path, array $body = [], array $headers = []): string|array
    {
        return $this->request('PATCH', $path, [
            'json' => $body,
            'headers' => $headers,
        ]);
    }

    public function delete(string $path, array $query = [], array $headers = []): string|array
    {
        return $this->request('DELETE', $path, [
            'query' => $query,
            'headers' => $headers,
        ]);
    }

    /**
     * Core request with unified error handling and OAuth2 bearer
     * @throws ApiException
     */
    public function request(string $method, string $path, array $options = []): string|array
    {
        $url = sprintf('%s/%s', $this->apiEndpoint, ltrim($path, '/'));

        $headers = array_change_key_case($options['headers'] ?? [], CASE_LOWER);
        $headers = array_merge([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'user-agent' => 'q-play-api-client/0.1 (+https://nordicscreen.com)'
        ], $headers);

        $attempts = 0;
        start:
        $attempts++;

        try {
            $response = $this->http()->request($method, $url, [
                'headers' => array_merge($headers, [
                    'authorization' => 'Bearer ' . $this->getToken(),
                ]),
                'query' => $options['query'] ?? null,
                // Prefer Guzzle's json option if provided
                'json' => $options['json'] ?? null,
                // Or raw body if given
                'body' => $options['body'] ?? null,
                'timeout' => $options['timeout'] ?? 30,
            ]);

            $body = (string)$response->getBody();
            $decoded = $body !== '' ? json_decode($body, true) : null;
            return $decoded === null ? $body : $decoded;
        } catch (RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $payload = $this->decodeError($e);

            // Retry once on 401 after clearing token
            if ($status === 401 && $attempts < 2) {
                $this->clearToken();
                goto start;
            }

            throw $this->mapException($status, $payload, $e);
        } catch (GuzzleException $e) {
            throw new ServerErrorException('Transport error: ' . $e->getMessage(), 0, [], $e);
        }
    }

    private function decodeError(RequestException $e): array
    {
        $resp = $e->getResponse();
        if (!$resp) {
            return ['message' => $e->getMessage()];
        }
        $body = (string)$resp->getBody();
        $data = json_decode($body, true);
        if (is_array($data)) {
            // our API wraps as { meta: { error, details? }, ... }
            $meta = $data['meta'] ?? [];
            $message = $meta['error'] ?? ($data['message'] ?? 'Request failed');
            $details = $meta['details'] ?? null;
            return ['message' => (string)$message, 'details' => $details, 'raw' => $data];
        }
        return ['message' => $body ?: 'Request failed'];
    }

    private function mapException(int $status, array $payload, ?Throwable $previous): ApiException
    {
        $message = $payload['message'] ?? 'Request failed';
        $details = $payload['details'] ?? [];
        return match ($status) {
            400 => new BadRequestException($message, $status, $details, $previous),
            401 => new UnauthorizedException($message, $status, $details, $previous),
            403 => new ForbiddenException($message, $status, $details, $previous),
            404 => new NotFoundException($message, $status, $details, $previous),
            405 => new MethodNotAllowedException($message, $status, $details, $previous),
            422 => new ValidationException($message, $status, $details, $previous),
            429 => new TooManyRequestsException($message, $status, $details, $previous),
            500, 501, 502, 503, 504 => new ServerErrorException($message, $status, $details, $previous),
            default => new ApiException($message, $status, $details, $previous),
        };
    }

    private function getToken(): string
    {
        $now = time();
        if ($this->token && $this->tokenExpiresAt && $now < $this->tokenExpiresAt - 30) {
            return $this->token;
        }

        // Try cache item if provided
        if ($this->tokenCacheItem && $this->tokenCacheItem->isHit()) {
            $cached = $this->tokenCacheItem->get();
            if (is_array($cached)) {
                $cachedToken = isset($cached['token']) ? (string)$cached['token'] : null;
                $cachedExp = isset($cached['expiresAt']) ? (int)$cached['expiresAt'] : null;
                if ($cachedToken && $cachedExp && $now < $cachedExp - 30) {
                    $this->token = $cachedToken;
                    $this->tokenExpiresAt = $cachedExp;
                    return $this->token;
                }
            }
        }

        try {
            $url = sprintf('%s/oauth/token', $this->apiEndpoint);
            $form = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ];
            if (!empty($this->scopes)) {
                $form['scope'] = implode(' ', $this->scopes);
            }

            $response = $this->http()->request('POST', $url, [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'form_params' => $form,
                'timeout' => 15,
            ]);

            $data = json_decode((string)$response->getBody(), true);
            if (!is_array($data) || empty($data['access_token'])) {
                throw new UnauthorizedException('Unable to obtain access token');
            }
            $this->token = (string)$data['access_token'];
            $expiresIn = isset($data['expires_in']) ? (int)$data['expires_in'] : 3600;
            $this->tokenExpiresAt = time() + max(60, $expiresIn);

            // Store in cache item if provided (note: persistence depends on the cache implementation)
            if ($this->tokenCacheItem) {
                $payload = [
                    'token' => $this->token,
                    'expiresAt' => $this->tokenExpiresAt,
                ];
                $this->tokenCacheItem->set($payload);
                // best-effort expiry alignment
                try {
                    $expiry = new \DateTimeImmutable('@' . $this->tokenExpiresAt);
                    $this->tokenCacheItem->expiresAt($expiry);
                } catch (Throwable) {
                    // ignore
                }
            }
            return $this->token;
        } catch (RequestException $e) {
            $payload = $this->decodeError($e);
            throw new UnauthorizedException(
                'Token request failed: ' . ($payload['message'] ?? $e->getMessage()),
                401,
                $payload['details'] ?? [],
                $e
            );
        } catch (GuzzleException $e) {
            throw new UnauthorizedException('Token transport error: ' . $e->getMessage(), 0, [], $e);
        }
    }

    private function clearToken(): void
    {
        $this->token = null;
        $this->tokenExpiresAt = null;
        if ($this->tokenCacheItem) {
            // Invalidate the cache item by expiring it immediately
            try {
                $this->tokenCacheItem->set(null);
                $this->tokenCacheItem->expiresAt(new \DateTimeImmutable('@0'));
            } catch (Throwable) {
                // ignore
            }
        }
    }

    private function http(): GuzzleHttpClient
    {
        if (!$this->httpClient) {
            $this->httpClient = new GuzzleHttpClient();
        }
        return $this->httpClient;
    }
}
