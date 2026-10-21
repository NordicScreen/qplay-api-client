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
use QPlay\Api\Client\Sections\Developer\AppMediaSection as DevAppMediaSection;
use QPlay\Api\Client\Sections\Developer\AppTemplateMediaSection as DevAppTemplateMediaSection;
use QPlay\Api\Client\Sections\IntegrationSection;
use QPlay\Api\Client\Sections\IntegrationTemplateSection;
use QPlay\Api\Client\Sections\MediaLibrarySection;
use QPlay\Api\Client\Sections\PlayersSection;
use QPlay\Api\Client\Sections\PlaylistSection;
use QPlay\Api\Client\Sections\PresentationsSection;
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
    private ?PlaylistSection $playlist = null;
    private ?IntegrationSection $integration = null;
    private ?IntegrationTemplateSection $integrationTemplate = null;
    private ?AppSection $app = null;
    private ?AppTemplateSection $appTemplate = null;
    private ?DevIntegrationTemplateSection $developerIntegrationTemplate = null;
    private ?DevAppSection $developerApp = null;
    private ?DevAppTemplateSection $developerAppTemplate = null;
    private ?DevAppMediaSection $developerAppMedia = null;
    private ?DevAppTemplateMediaSection $developerAppTemplateMedia = null;
    private ?SystemSection $system = null;
    private ?string $token = null;
    private ?int $tokenExpiresAt = null;
    private ?CacheItemInterface $tokenCacheItem = null;

    /**
     * Construct a new API client instance.
     *
     * @param string $apiEndpoint The base URL of the Q-Play REST API
     * @param string $clientId OAuth2 client ID
     * @param string $clientSecret OAuth2 client secret
     * @param string[] $scopes OAuth2 scopes to request
     * @param CacheItemInterface|null $tokenCacheItem Optional PSR-6 cache item for token persistence
     * @param GuzzleHttpClient|null $httpClient Optional custom HTTP client for testing or customization
     */
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
     *
     * @return MediaLibrarySection The media library section handler
     */
    public function media(): MediaLibrarySection
    {
        return $this->media ??= new MediaLibrarySection($this, 'media');
    }

    /**
     * Players API (/players)
     *
     * @return PlayersSection The players section handler
     */
    public function players(): PlayersSection
    {
        return $this->players ??= new PlayersSection($this, 'players');
    }

    /**
     * Presentations API (/presentation)
     *
     * @return PresentationsSection The presentations section handler
     */
    public function presentations(): PresentationsSection
    {
        return $this->presentations ??= new PresentationsSection($this, 'presentation');
    }

    /**
     * Playlist API (/playlist)
     *
     * @return PlaylistSection The playlist section handler
     */
    public function playlist(): PlaylistSection
    {
        return $this->playlist ??= new PlaylistSection($this, 'playlist');
    }

    /**
     * Integration API (/integration)
     *
     * @return IntegrationSection The integration section handler
     */
    public function integration(): IntegrationSection
    {
        return $this->integration ??= new IntegrationSection($this, 'integration');
    }

    /**
     * Integration Template API (/integration-template)
     *
     * @return IntegrationTemplateSection The integration template section handler
     */
    public function integrationTemplate(): IntegrationTemplateSection
    {
        return $this->integrationTemplate ??= new IntegrationTemplateSection($this, 'integration-template');
    }

    /**
     * App API (/app) — read-only
     *
     * @return AppSection The app section handler
     */
    public function app(): AppSection
    {
        return $this->app ??= new AppSection($this, 'app');
    }

    /**
     * App Template API (/app-template)
     *
     * @return AppTemplateSection The app template section handler
     */
    public function appTemplate(): AppTemplateSection
    {
        return $this->appTemplate ??= new AppTemplateSection($this, 'app-template');
    }

    /**
     * System API (/status, /health)
     *
     * @return SystemSection The system section handler
     */
    public function system(): SystemSection
    {
        return $this->system ??= new SystemSection($this, '');
    }

    /**
     * Developer App API (/developer/app)
     *
     * @return DevAppSection The developer app section handler
     */
    public function developerApp(): DevAppSection
    {
        return $this->developerApp
            ??= new DevAppSection($this, 'developer/app');
    }

    /**
     * Developer App Template API (/developer/app-template)
     *
     * @return DevAppTemplateSection The developer app template section handler
     */
    public function developerAppTemplate(): DevAppTemplateSection
    {
        return $this->developerAppTemplate
            ??= new DevAppTemplateSection($this, 'developer/app-template');
    }

    /**
     * Developer Integration Template API (/developer/integration-template)
     *
     * @return DevIntegrationTemplateSection The developer integration template section handler
     */
    public function developerIntegrationTemplate(): DevIntegrationTemplateSection
    {
        return $this->developerIntegrationTemplate
            ??= new DevIntegrationTemplateSection($this, 'developer/integration-template');
    }

    /**
     * Developer App Media API (/developer/app)
     *
     * @return DevAppMediaSection The developer app media section handler
     */
    public function developerAppMedia(): DevAppMediaSection
    {
        return $this->developerAppMedia
            ??= new DevAppMediaSection($this, 'developer/app');
    }

    /**
     * Developer App Template Media API (/developer/app-template)
     *
     * @return DevAppTemplateMediaSection The developer app template media section handler
     */
    public function developerAppTemplateMedia(): DevAppTemplateMediaSection
    {
        return $this->developerAppTemplateMedia
            ??= new DevAppTemplateMediaSection($this, 'developer/app-template');
    }

    /**
     * Send a GET request to the API.
     *
     * @param string $path The API endpoint path
     * @param array $query Query parameters to append to the URL
     * @param array $headers Additional HTTP headers
     * @return string|array The decoded JSON response or raw response body
     * @throws ApiException
     */
    public function get(string $path, array $query = [], array $headers = []): string|array
    {
        return $this->request('GET', $path, [
            'query' => $query,
            'headers' => $headers,
        ]);
    }

    /**
     * Send a POST request to the API.
     *
     * @param string $path The API endpoint path
     * @param array $body The request body as an associative array (JSON-encoded)
     * @param array $headers Additional HTTP headers
     * @return string|array The decoded JSON response or raw response body
     * @throws ApiException
     */
    public function post(string $path, array $body = [], array $headers = []): string|array
    {
        return $this->request('POST', $path, [
            'json' => $body,
            'headers' => $headers,
        ]);
    }

    /**
     * Send a PUT request to the API.
     *
     * @param string $path The API endpoint path
     * @param array $body The request body as an associative array (JSON-encoded)
     * @param array $headers Additional HTTP headers
     * @return string|array The decoded JSON response or raw response body
     * @throws ApiException
     */
    public function put(string $path, array $body = [], array $headers = []): string|array
    {
        return $this->request('PUT', $path, [
            'json' => $body,
            'headers' => $headers,
        ]);
    }

    /**
     * Send a PATCH request to the API.
     *
     * @param string $path The API endpoint path
     * @param array $body The request body as an associative array (JSON-encoded)
     * @param array $headers Additional HTTP headers
     * @return string|array The decoded JSON response or raw response body
     * @throws ApiException
     */
    public function patch(string $path, array $body = [], array $headers = []): string|array
    {
        return $this->request('PATCH', $path, [
            'json' => $body,
            'headers' => $headers,
        ]);
    }

    /**
     * Send a DELETE request to the API.
     *
     * @param string $path The API endpoint path
     * @param array $query Query parameters to append to the URL
     * @param array $headers Additional HTTP headers
     * @return string|array The decoded JSON response or raw response body
     * @throws ApiException
     */
    public function delete(string $path, array $query = [], array $headers = []): string|array
    {
        return $this->request('DELETE', $path, [
            'query' => $query,
            'headers' => $headers,
        ]);
    }

    /**
     * Core request with unified error handling and OAuth2 bearer token.
     *
     * @param string $method HTTP method (GET, POST, PUT, PATCH, DELETE)
     * @param string $path The API endpoint path
     * @param array $options Request options (query, headers, json, body, timeout)
     * @return string|array The decoded JSON response or raw response body
     * @throws ApiException On HTTP error responses
     * @throws ServerErrorException On transport errors
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

    /**
     * Decode error payload from a RequestException response.
     *
     * @param RequestException $e The request exception containing the error response
     * @return array Associative array with 'message', 'details', and optionally 'raw' keys
     */
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

    /**
     * Map an HTTP status code to the appropriate ApiException subclass.
     *
     * @param int $status The HTTP status code
     * @param array $payload The decoded error payload containing message and details
     * @param Throwable|null $previous The previous exception for chaining
     * @return ApiException The mapped exception instance
     */
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

    /**
     * Obtain or refresh the OAuth2 access token.
     *
     * @return string The valid access token
     * @throws UnauthorizedException If token acquisition fails
     */
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

    /**
     * Clear the cached access token and expiration time.
     */
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

    /**
     * Get the underlying HTTP client, lazily creating it if needed.
     *
     * @return GuzzleHttpClient The HTTP client instance
     */
    private function http(): GuzzleHttpClient
    {
        if (!$this->httpClient) {
            $this->httpClient = new GuzzleHttpClient();
        }
        return $this->httpClient;
    }
}
