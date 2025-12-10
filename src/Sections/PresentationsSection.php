<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Presentation section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class PresentationsSection extends AbstractSection
{
    /**
     * List presentations.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a presentation by id.
     *
     * @param int|string $id Presentation identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Create a presentation.
     *
     * @param array $data Presentation payload
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update a presentation.
     *
     * @param int|string $id Presentation identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $id, array $data): string|array
    {
        return $this->put((string)$id, $data);
    }

    /**
     * Delete a presentation.
     *
     * @param int|string $id Presentation identifier
     * @return string|array API response
     */
    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
    }
}
