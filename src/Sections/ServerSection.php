<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Integration server section for server discovery, state, stats, and communications.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class ServerSection extends AbstractSection
{
    /**
     * List all integration servers.
     *
     * @param array $query Optional query parameters
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a specific integration server by id.
     *
     * @param int|string $id Server identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Get the state of an integration server.
     *
     * @param int|string $id Server identifier
     * @return string|array API response
     */
    public function getState(int|string $id): string|array
    {
        return $this->get($id . '/state');
    }

    /**
     * Get stats for an integration server.
     *
     * @param int|string $id Server identifier
     * @return string|array API response
     */
    public function getStats(int|string $id): string|array
    {
        return $this->get($id . '/stats');
    }

    /**
     * Send a heartbeat for an integration server.
     *
     * @param int|string $id Server identifier
     * @param array $data Heartbeat payload
     * @return string|array API response
     */
    public function heartbeat(int|string $id, array $data = []): string|array
    {
        return $this->post($id . '/heartbeat', $data);
    }

    /**
     * Send/receive a message on an integration server (PUT).
     *
     * @param int|string $id Server identifier
     * @param array $data Message payload
     * @return string|array API response
     */
    public function receiveMessage(int|string $id, array $data): string|array
    {
        return $this->put($id . '/message', $data);
    }
}
