<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Event section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class EventsSection extends AbstractSection
{
    /**
     * List events.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get an event by id.
     *
     * @param int|string $id Event identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Create an event.
     *
     * @param array $data Event payload
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update an event.
     *
     * @param int|string $id Event identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $id, array $data): string|array
    {
        return $this->put((string)$id, $data);
    }

    /**
     * Delete an event by id.
     *
     * @param int|string $id Event identifier
     * @return string|array API response
     */
    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
    }
}
