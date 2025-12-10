<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Sections\AbstractSection;

/**
 * App section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections\Developer
 */
class AppSection extends AbstractSection
{
    /**
     * List all apps.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a specific app by id.
     *
     * @param int|string $id App identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Create a new app.
     *
     * @param array $data Payload for the app to create
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update an existing app by id.
     *
     * @param int|string $id App identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $id, array $data): string|array
    {
        // Prefer PATCH for updates
        return $this->patch((string)$id, $data);
    }

    /**
     * Delete an app by id.
     *
     * @param int|string $id App identifier
     * @return string|array API response
     */
    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
    }

    /**
     * List versions for a given app.
     *
     * @param int|string $appId App identifier
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function listVersions(int|string $appId, array $query = []): string|array
    {
        return $this->get($appId . '/version', $query);
    }

    /**
     * Get a specific version for a given app.
     *
     * @param int|string $appId App identifier
     * @param int|string $version Major version
     * @param int|string $patchVersion Patch version
     * @return string|array API response
     */
    public function getVersion(int|string $appId, int|string $version, int|string $patchVersion): string|array
    {
        return $this->get($appId . '/version/' . $version . '.' . $patchVersion);
    }

    /**
     * Create a new version for a given app.
     *
     * @param int|string $appId App identifier
     * @param array $data Version payload
     * @return string|array API response
     */
    public function createVersion(int|string $appId, array $data): string|array
    {
        return $this->post($appId . '/version', $data);
    }

    /**
     * Update a specific version for a given app.
     *
     * @param int|string $appId App identifier
     * @param int|string $version Major version
     * @param int|string $patchVersion Patch version
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function updateVersion(
        int|string $appId,
        int|string $version,
        int|string $patchVersion,
        array $data
    ): string|array {
        return $this->patch($appId . '/version/' . $version . '.' . $patchVersion, $data);
    }

    /**
     * Delete a specific version for a given app.
     *
     * @param int|string $appId App identifier
     * @param int|string $version Major version
     * @param int|string $patchVersion Patch version
     * @return string|array API response
     */
    public function deleteVersion(int|string $appId, int|string $version, int|string $patchVersion): string|array
    {
        return $this->delete($appId . '/version/' . $version . '.' . $patchVersion);
    }
}
