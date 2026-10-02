<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Playlist section with CRUD operations.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class PlaylistSection extends AbstractSection
{
    /**
     * List playlists.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a playlist by id.
     *
     * @param int|string $id Playlist identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Create a playlist.
     *
     * @param array $data Playlist payload
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update a playlist (PATCH).
     *
     * @param int|string $id Playlist identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $id, array $data): string|array
    {
        return $this->patch((string)$id, $data);
    }

    /**
     * Delete a playlist by id.
     *
     * @param int|string $id Playlist identifier
     * @return string|array API response
     */
    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
    }
}
