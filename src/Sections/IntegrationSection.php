<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

use QPlay\Api\Client\Exception\ApiException;

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
     * @throws ApiException
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
     * @throws ApiException
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Trigger a run for an integration.
     *
     * @param int|string $id Integration identifier
     * @param array $payload Optional payload for the run
     * @return string|array API response
     * @throws ApiException
     * @deprecated The `/integration/{id}/run` endpoint is not defined in the API spec.
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
     * @throws ApiException
     * @deprecated The `/integration/{id}/statistics` endpoint is not defined in the API spec.
     */
    public function statistics(int|string $id, array $query = []): string|array
    {
        return $this->get($id . '/statistics', $query);
    }
}
