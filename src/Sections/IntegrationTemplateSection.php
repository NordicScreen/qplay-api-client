<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Integration template section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class IntegrationTemplateSection extends AbstractSection
{
    /**
     * List all integration templates.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a specific integration template by id.
     *
     * @param int|string $id Template identifier
     * @return string|array API response
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * List versions for a given integration template.
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
     * Get a specific version for a template.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @return string|array API response
     */
    public function getVersion(int|string $templateId, int|string $versionId): string|array
    {
        return $this->get($templateId . '/version/' . $versionId);
    }

    /**
     * List scripts for a template version.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function listScripts(int|string $templateId, int|string $versionId, array $query = []): string|array
    {
        return $this->get($templateId . '/version/' . $versionId . '/script', $query);
    }

    /**
     * Get a specific script by identifier.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @param int|string $identifier Script identifier
     * @return string|array API response
     */
    public function getScript(int|string $templateId, int|string $versionId, int|string $identifier): string|array
    {
        return $this->get($templateId . '/version/' . $versionId . '/script/' . $identifier);
    }
}
