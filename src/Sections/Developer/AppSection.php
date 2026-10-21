<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Exception\ApiException;
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
     */
    public function update(int|string $id, array $data): string|array
    {
        return $this->patch((string)$id, $data);
    }

    /**
     * Delete an app by id.
     *
     * @param int|string $id App identifier
     * @return string|array API response
     * @throws ApiException
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
     * @throws ApiException
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
     * @param int|string|null $patchVersion Patch version (optional if version contains full semver)
     * @return string|array API response
     * @throws ApiException
     */
    public function getVersion(int|string $appId, int|string $version, int|string|null $patchVersion = null): string|array
    {
        $versionStr = $patchVersion !== null ? $version . '.' . $patchVersion : (string)$version;
        return $this->get($appId . '/version/' . $versionStr);
    }

    /**
     * Create a new version for a given app.
     *
     * @param int|string $appId App identifier
     * @param array $data Version payload
     * @return string|array API response
     * @throws ApiException
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
     * @param int|string|null $patchVersion Patch version (or pass $data as 3rd param if full version string is used)
     * @param array $data Fields to update
     * @return string|array API response
     * @throws ApiException
     */
    public function updateVersion(
        int|string $appId,
        int|string $version,
        int|string|array $patchVersion,
        array $data = []
    ): string|array {
        if (is_array($patchVersion)) {
            return $this->patch($appId . '/version/' . $version, $patchVersion);
        }
        return $this->patch($appId . '/version/' . $version . '.' . $patchVersion, $data);
    }

    /**
     * Delete a specific version for a given app.
     *
     * @param int|string $appId App identifier
     * @param int|string $version Major version
     * @param int|string|null $patchVersion Patch version
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteVersion(int|string $appId, int|string $version, int|string|null $patchVersion = null): string|array
    {
        $versionStr = $patchVersion !== null ? $version . '.' . $patchVersion : (string)$version;
        return $this->delete($appId . '/version/' . $versionStr);
    }
}
