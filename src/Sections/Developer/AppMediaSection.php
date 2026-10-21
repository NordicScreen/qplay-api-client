<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Exception\ApiException;
use QPlay\Api\Client\Sections\AbstractSection;

/**
 * Developer app media section for app media assets and icons.
 *
 * @namespace QPlay\Api\Client\Sections\Developer
 */
class AppMediaSection extends AbstractSection
{
    /**
     * Get media assets for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @return string|array API response
     * @throws ApiException
     */
    public function getMedia(int|string $appId, string $version): string|array
    {
        return $this->get($appId . '/version/' . $version . '/media');
    }

    /**
     * Upload a media asset for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @param array $data Upload payload
     * @return string|array API response
     * @throws ApiException
     */
    public function uploadMedia(int|string $appId, string $version, array $data): string|array
    {
        return $this->post($appId . '/version/' . $version . '/media', $data);
    }

    /**
     * Delete a media asset for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @param string $fileUuid File UUID
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteMedia(int|string $appId, string $version, string $fileUuid): string|array
    {
        return $this->delete($appId . '/version/' . $version . '/media/' . $fileUuid);
    }

    /**
     * Upload an app icon for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @param array $data Icon payload
     * @return string|array API response
     * @throws ApiException
     */
    public function uploadIcon(int|string $appId, string $version, array $data): string|array
    {
        return $this->post($appId . '/version/' . $version . '/media/icon', $data);
    }

    /**
     * Remove an app icon for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @return string|array API response
     * @throws ApiException
     */
    public function removeIcon(int|string $appId, string $version): string|array
    {
        return $this->delete($appId . '/version/' . $version . '/media/icon');
    }
}
