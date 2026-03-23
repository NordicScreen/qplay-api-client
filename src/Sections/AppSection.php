<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * App section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
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
     * @param string $id App identifier
     * @return string|array API response
     */
    public function getById(string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * List versions for a given app.
     *
     * @param string $appId App identifier
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function listVersions(string $appId, array $query = []): string|array
    {
        return $this->get($appId . '/version', $query);
    }

    /**
     * Get a specific version for a given app.
     *
     * @param string $appId App identifier
     * @param string $version Major version
     * @param int|string $patchVersion Patch version
     * @return string|array API response
     */
    public function getVersion(string $appId, string $version, int|string $patchVersion): string|array
    {
        return $this->get($appId . '/version/' . $version . '.' . $patchVersion);
    }
}
