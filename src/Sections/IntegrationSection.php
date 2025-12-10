<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Integration section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class IntegrationSection extends AbstractSection
{
    /**
     * List all integrations.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a specific integration by id.
     *
     * @param int|string $id Integration identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Create a new integration.
     *
     * @param array $data Integration payload
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update an existing integration.
     *
     * @param int|string $id Integration identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $id, array $data): string|array
    {
        return $this->put((string)$id, $data);
    }

    /**
     * Delete an integration by id.
     *
     * @param int|string $id Integration identifier
     * @return string|array API response
     */
    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
    }

    /**
     * Trigger a run for an integration.
     *
     * @param int|string $id Integration identifier
     * @param array $payload Optional payload for the run
     * @return string|array API response
     */
    public function run(int|string $id, array $payload = []): string|array
    {
        return $this->post($id . '/run', $payload);
    }

    /**
     * Fetch statistics for an integration.
     *
     * @param int|string $id Integration identifier
     * @param array $query Optional query parameters (e.g. date ranges)
     * @return string|array API response
     */
    public function statistics(int|string $id, array $query = []): string|array
    {
        return $this->get($id . '/statistics', $query);
    }
}
