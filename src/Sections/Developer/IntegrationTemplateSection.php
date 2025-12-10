<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Sections\AbstractSection;

/**
 * Integration template section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections\Developer
 */
class IntegrationTemplateSection extends AbstractSection
{
    /**
     * List integration templates.
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
     * Create a new integration template.
     *
     * @param array $data Payload for the template to create
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update an existing integration template by id (PATCH).
     *
     * @param int|string $id Template identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $id, array $data): string|array
    {
        // Always PATCH
        return $this->patch((string)$id, $data);
    }

    /**
     * Delete an integration template by id.
     *
     * @param int|string $id Template identifier
     * @return string|array API response
     */
    public function deleteById(int|string $id): string|array
    {
        return $this->delete((string)$id);
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
     * Create a new version under a template.
     *
     * @param int|string $templateId Template identifier
     * @param array $data Version payload
     * @return string|array API response
     */
    public function createVersion(int|string $templateId, array $data)
    {
        return $this->post($templateId . '/version', $data);
    }

    /**
     * Update a version (PATCH).
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function updateVersion(int|string $templateId, int|string $versionId, array $data): string|array
    {
        return $this->patch($templateId . '/version/' . $versionId, $data);
    }

    /**
     * Delete a version.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @return string|array API response
     */
    public function deleteVersion(int|string $templateId, int|string $versionId): string|array
    {
        return $this->delete($templateId . '/version/' . $versionId);
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
     * Create a script for a template version.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @param array $data Script payload
     * @return string|array API response
     */
    public function createScript(int|string $templateId, int|string $versionId, array $data): string|array
    {
        return $this->post($templateId . '/version/' . $versionId . '/script', $data);
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

    /**
     * Update a script (PATCH).
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @param int|string $identifier Script identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function updateScript(
        int|string $templateId,
        int|string $versionId,
        int|string $identifier,
        array $data
    ): string|array {
        return $this->patch($templateId . '/version/' . $versionId . '/script/' . $identifier, $data);
    }

    /**
     * Delete a script.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $versionId Version identifier
     * @param int|string $identifier Script identifier
     * @return string|array API response
     */
    public function deleteScript(int|string $templateId, int|string $versionId, int|string $identifier): string|array
    {
        return $this->delete($templateId . '/version/' . $versionId . '/script/' . $identifier);
    }
}
