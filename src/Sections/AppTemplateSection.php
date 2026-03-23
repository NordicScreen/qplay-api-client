<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * App template section with basic CRUD and version helpers.
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
     * @param int|string $patchVersion Patch version
     * @return string|array API response
     */
    public function getVersion(int|string $templateId, int|string $version, int|string $patchVersion): string|array
    {
        return $this->get($templateId . '/version/' . $version . '.' . $patchVersion);
    }
}
