<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Exception\ApiException;
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
     * Get a specific app template by id.
     *
     * @param int|string $id Template identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function getById(int|string $id): string|array
    {
        return $this->get((string)$id);
    }

    /**
     * Create a new app template.
     *
     * @param array $data Payload for the template to create
     * @return string|array API response
     * @throws ApiException
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update an existing app template by id.
     *
     * @param int|string $id Template identifier
     * @param array $data Fields to update
     * @return string|array API response
     * @throws ApiException
     */
    public function update(int|string $id, array $data): string|array
    {
        return $this->patch((string)$id, $data);
    }

    /**
     * Delete an app template by id.
     *
     * @param int|string $id Template identifier
     * @return string|array API response
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
     */
    public function getVersion(
        int|string $templateId,
        int|string $version,
        int|string $patchVersion
    ): string|array {
        return $this->get($templateId . '/version/' . $version . '.' . $patchVersion);
    }

    /**
     * Create a new version for a given app template.
     *
     * @param int|string $templateId Template identifier
     * @param array $data Version payload
     * @return string|array API response
     * @throws ApiException
     */
    public function createVersion(int|string $templateId, array $data): string|array
    {
        return $this->post($templateId . '/version', $data);
    }

    /**
     * Update a specific version for a given app template.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $version Major version
     * @param int|string $patchVersion Patch version
     * @param array $data Fields to update
     * @return string|array API response
     * @throws ApiException
     */
    public function updateVersion(
        int|string $templateId,
        int|string $version,
        int|string $patchVersion,
        array $data
    ): string|array {
        return $this->patch($templateId . '/version/' . $version . '.' . $patchVersion, $data);
    }

    /**
     * Delete a specific version for a given app template.
     *
     * @param int|string $templateId Template identifier
     * @param int|string $version Major version
     * @param int|string $patchVersion Patch version
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteVersion(
        int|string $templateId,
        int|string $version,
        int|string $patchVersion
    ): string|array {
        return $this->delete($templateId . '/version/' . $version . '.' . $patchVersion);
    }
}
