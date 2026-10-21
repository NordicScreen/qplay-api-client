<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

use QPlay\Api\Client\Exception\ApiException;

/**
 * Player section with CRUD, status, command, and relation helpers.
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
     */
    public function update(int|string $playerId, array $data): string|array
    {
        return $this->patch((string)$playerId, $data);
    }

    /**
     * Delete a player by id.
     *
     * @param int|string $playerId Player identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteById(int|string $playerId): string|array
    {
        return $this->delete((string)$playerId);
    }

    /**
     * Get player runtime status.
     *
     * @param int|string $playerId Player identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function getStatus(int|string $playerId): string|array
    {
        return $this->get($playerId . '/status');
    }

    /**
     * Get player screenshot.
     *
     * @param int|string $playerId Player identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function getScreenshot(int|string $playerId): string|array
    {
        return $this->get($playerId . '/screenshot');
    }

    /**
     * Get player blackscreen schedule.
     *
     * @param int|string $playerId Player identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function getBlackscreenSchedule(int|string $playerId): string|array
    {
        return $this->get($playerId . '/blackscreen-schedule');
    }

    /**
     * Send command to a player.
     *
     * @param int|string $playerId Player identifier
     * @param array $data Command payload
     * @return string|array API response
     * @throws ApiException
     */
    public function sendCommand(int|string $playerId, array $data = []): string|array
    {
        return $this->post($playerId . '/commands', $data);
    }

    /**
     * Unlink a presentation from a player.
     *
     * @param int|string $playerId Player identifier
     * @param int|string $presentationVersionId Presentation version identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function unlinkPresentation(int|string $playerId, int|string $presentationVersionId): string|array
    {
        return $this->delete($playerId . '/presentations/' . $presentationVersionId);
    }

    /**
     * Unlink an event from a player.
     *
     * @param int|string $playerId Player identifier
     * @param int|string $eventId Event identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function unlinkEvent(int|string $playerId, int|string $eventId): string|array
    {
        return $this->delete($playerId . '/events/' . $eventId);
    }
}
