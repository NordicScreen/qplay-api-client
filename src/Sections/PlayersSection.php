<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Player section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class PlayersSection extends AbstractSection
{
    /**
     * List players.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a player by id.
     *
     * @param int|string $playerId Player identifier
     * @return string|array API response
     */
    public function getById(int|string $playerId): string|array
    {
        return $this->get((string)$playerId);
    }

    /**
     * Create a player.
     *
     * @param array $data Player payload
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update a player.
     *
     * @param int|string $playerId Player identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $playerId, array $data): string|array
    {
        return $this->put((string)$playerId, $data);
    }

    /**
     * Delete a player by id.
     *
     * @param int|string $playerId Player identifier
     * @return string|array API response
     */
    public function deleteById(int|string $playerId): string|array
    {
        return $this->delete((string)$playerId);
    }
}
