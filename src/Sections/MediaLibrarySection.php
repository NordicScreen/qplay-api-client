<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

/**
 * Media library section with basic CRUD and version helpers.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class MediaLibrarySection extends AbstractSection
{
    /**
     * List media assets.
     *
     * @param array $query Optional query parameters (e.g. filters, pagination)
     * @return string|array API response
     */
    public function list(array $query = []): string|array
    {
        return $this->get('', $query);
    }

    /**
     * Get a media asset by id.
     *
     * @param int|string $mediaId Media identifier
     * @return string|array API response
     */
    public function getById(int|string $mediaId): string|array
    {
        return $this->get((string)$mediaId);
    }

    /**
     * Create a media asset.
     *
     * @param array $data Media payload
     * @return string|array API response
     */
    public function create(array $data): string|array
    {
        return $this->post('', $data);
    }

    /**
     * Update a media asset.
     *
     * @param int|string $mediaId Media identifier
     * @param array $data Fields to update
     * @return string|array API response
     */
    public function update(int|string $mediaId, array $data): string|array
    {
        return $this->put((string)$mediaId, $data);
    }

    /**
     * Delete a media asset.
     *
     * @param int|string $mediaId Media identifier
     * @return string|array API response
     */
    public function deleteById(int|string $mediaId): string|array
    {
        return $this->delete((string)$mediaId);
    }

    /**
     * Download a media asset.
     *
     * @param int|string $mediaId Media identifier
     * @return string|array API response (binary stream or link, depending on client configuration)
     */
    public function download(int|string $mediaId): string|array
    {
        return $this->get($mediaId . '/download');
    }
}
