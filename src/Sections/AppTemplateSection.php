<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * App template section with CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class AppTemplateSection extends AbstractSection
{
    /**
     * List all app templates.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a specific app template by id.
     *
     * @param int|string $id Template identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Create an app template.
     *
     * @param array $data Template payload
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update an app template (PUT).
     *
     * @param int|string $id Template identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $id, array $data): string|array
    {
        return $this->put((string)$id, $data);
    }

    /**
     * Delete an app template by id.
     *
     * @param int|string $id Template identifier
     * @return string|array API response
     */
    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
    }

    /**
     * List versions for a given app template.
     *
     * @param int|string $templateId Template identifier
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function listVersions(int|string $templateId, array $query = []): string|array
    {
        return $this->get($templateId . '/version', $query);
    }

    /**
     * Get a specific version for a given app template.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $version Major version
     * @param int|string|null $patchVersion Patch version (optional if version contains full semver)
     * @return string|array API response
     */
    public function getVersion(
        int|string $templateId,
        int|string $version,
        int|string|null $patchVersion = null
    ): string|array {
        $versionStr = $patchVersion !== null ? $version . '.' . $patchVersion : (string)$version;
        return $this->get($templateId . '/version/' . $versionStr);
    }

    /**
     * Create a version for a given app template.
     *
     * @param int|string $templateId Template identifier
     * @param array $data Version payload
     * @return string|array API response
     */
    public function createVersion(int|string $templateId, array $data): string|array
    {
        return $this->post($templateId . '/version', $data);
    }

    /**
     * Update a specific version for a given app template (PUT).
     *
     * @param int|string $templateId Template identifier
     * @param int|string $version Major version
     * @param int|string|array $patchVersion Patch version or data array
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function updateVersion(
        int|string $templateId,
        int|string $version,
        int|string|array $patchVersion,
        array $data = []
    ): string|array {
        if (is_array($patchVersion)) {
            return $this->put($templateId . '/version/' . $version, $patchVersion);
        }
        return $this->put($templateId . '/version/' . $version . '.' . $patchVersion, $data);
    }

    /**
     * Delete a specific version for a given app template.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $version Major version
     * @param int|string|null $patchVersion Patch version
     * @return string|array API response
     */
    public function deleteVersion(
        int|string $templateId,
        int|string $version,
        int|string|null $patchVersion = null
    ): string|array {
        $versionStr = $patchVersion !== null ? $version . '.' . $patchVersion : (string)$version;
        return $this->delete($templateId . '/version/' . $versionStr);
    }
}
