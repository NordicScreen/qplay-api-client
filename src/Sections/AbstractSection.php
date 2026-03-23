<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

use QPlay\Api\Client\Client;

/**
 * Base helper for namespaced API sections that share a path prefix and delegate to the core Client.
 *
 * @namespace QPlay\Api\Client\Sections
 */
abstract class AbstractSection
{
    protected Client $client;
    protected string $basePath; // without leading slash, e.g. "media"

    /**
     * Construct a section helper.
     *
     * @param Client $client The underlying HTTP API client
     * @param string $basePath Base path for this section (without leading slash)
     */
    public function __construct(Client $client, string $basePath)
    {
        $this->client = $client;
        $this->basePath = trim($basePath, '/');
    }

    /**
     * Compose a section-relative path under the section base.
     *
     * @param string $path Optional sub-path to append to the section base (leading/trailing slashes are trimmed)
     * @return string Fully qualified section path (without leading slash)
     */
    protected function path(string $path = ''): string
    {
        $path = trim($path, '/');
        return $path === '' ? $this->basePath : $this->basePath . '/' . $path;
    }

    /**
     * Generic request delegator with prefixing.
     *
     * @param string $method HTTP method (e.g. GET, POST, PUT, PATCH, DELETE)
     * @param string $path Optional path relative to the section base
     * @param array $options Request options forwarded to the client (e.g. query, headers, json/body)
     * @return string|array API response
     */
    public function request(string $method, string $path = '', array $options = []): string|array
    {
        return $this->client->request($method, $this->path($path), $options);
    }

    /**
     * Send a GET request for this section.
     *
     * @param string $path Optional path relative to the section base
     * @param array $query Query parameters
     * @param array $headers Additional headers
     * @return string|array API response
     */
    public function get(string $path = '', array $query = [], array $headers = []): string|array
    {
        return $this->client->get($this->path($path), $query, $headers);
    }

    /**
     * Send a POST request for this section.
     *
     * @param string $path Optional path relative to the section base
     * @param array $body Request payload/body
     * @param array $headers Additional headers
     * @return string|array API response
     */
    public function post(string $path = '', array $body = [], array $headers = []): string|array
    {
        return $this->client->post($this->path($path), $body, $headers);
    }

    /**
     * Send a PUT request for this section.
     *
     * @param string $path Optional path relative to the section base
     * @param array $body Request payload/body
     * @param array $headers Additional headers
     * @return string|array API response
     */
    public function put(string $path = '', array $body = [], array $headers = []): string|array
    {
        return $this->client->put($this->path($path), $body, $headers);
    }

    /**
     * Send a PATCH request for this section.
     *
     * @param string $path Optional path relative to the section base
     * @param array $body Request payload/body
     * @param array $headers Additional headers
     * @return string|array API response
     */
    public function patch(string $path = '', array $body = [], array $headers = []): string|array
    {
        return $this->client->patch($this->path($path), $body, $headers);
    }

    /**
     * Send a DELETE request for this section.
     *
     * @param string $path Optional path relative to the section base
     * @param array $query Query parameters
     * @param array $headers Additional headers
     * @return string|array API response
     */
    public function delete(string $path = '', array $query = [], array $headers = []): string|array
    {
        return $this->client->delete($this->path($path), $query, $headers);
    }
}
