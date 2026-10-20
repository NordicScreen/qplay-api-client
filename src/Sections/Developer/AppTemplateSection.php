<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Sections\AbstractSection;

/**
 * App template section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections\Developer
 */
class AppTemplateSection extends AbstractSection
{
    /**
     * List all app templates.
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a specific app template by id.
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    public function update(int|string $id, array $data): string|array
    {
        return $this->patch((string)$id, $data);
    }

    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
    }

    /**
     * List versions for a given app template.
     */
    public function listVersions(int|string $templateId, array $query = []): string|array
    {
        return $this->get($templateId . '/version', $query);
    }

    /**
     * Get a specific version for a given app template.
     */
    public function getVersion(
        int|string $templateId,
        int|string $version,
        int|string|null $patchVersion = null
    ): string|array {
        $versionStr = $patchVersion !== null ? $version . '.' . $patchVersion : (string)$version;
        return $this->get($templateId . '/version/' . $versionStr);
    }

    public function createVersion(int|string $templateId, array $data): string|array
    {
        return $this->post($templateId . '/version', $data);
    }

    public function updateVersion(
        int|string $templateId,
        int|string $version,
        int|string|array $patchVersion,
        array $data = []
    ): string|array {
        if (is_array($patchVersion)) {
            return $this->patch($templateId . '/version/' . $version, $patchVersion);
        }
        return $this->patch($templateId . '/version/' . $version . '.' . $patchVersion, $data);
    }

    public function deleteVersion(
        int|string $templateId,
        int|string $version,
        int|string|null $patchVersion = null
    ): string|array {
        $versionStr = $patchVersion !== null ? $version . '.' . $patchVersion : (string)$version;
        return $this->delete($templateId . '/version/' . $versionStr);
    }
}
