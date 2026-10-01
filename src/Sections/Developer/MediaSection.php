<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Sections\AbstractSection;

/**
 * Developer media section for app/template media assets and icons.
 *
 * @namespace QPlay\Api\Client\Sections\Developer
 */
class MediaSection extends AbstractSection
{
    /**
     * Get media assets for an app template version.
     *
     * @param int|string $templateId Template identifier
     * @param string $version Template version string
     * @return string|array API response
     */
    public function getTemplateMedia(int|string $templateId, string $version): string|array
    {
        return $this->get('template/' . $templateId . '/' . $version);
    }

    /**
     * Upload a media asset for an app template version.
     *
     * @param int|string $templateId Template identifier
     * @param string $version Template version string
     * @param array $data Upload payload
     * @return string|array API response
     */
    public function uploadTemplateMedia(int|string $templateId, string $version, array $data): string|array
    {
        return $this->post('template/' . $templateId . '/' . $version, $data);
    }

    /**
     * Delete a media asset for an app template version.
     *
     * @param int|string $templateId Template identifier
     * @param string $version Template version string
     * @param string $fileUuid File UUID
     * @return string|array API response
     */
    public function deleteTemplateMedia(int|string $templateId, string $version, string $fileUuid): string|array
    {
        return $this->delete('template/' . $templateId . '/' . $version . '/' . $fileUuid);
    }

    /**
     * Get media assets for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @return string|array API response
     */
    public function getAppMedia(int|string $appId, string $version): string|array
    {
        return $this->get($appId . '/' . $version);
    }

    /**
     * Upload a media asset for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @param array $data Upload payload
     * @return string|array API response
     */
    public function uploadAppMedia(int|string $appId, string $version, array $data): string|array
    {
        return $this->post($appId . '/' . $version, $data);
    }

    /**
     * Delete a media asset for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @param string $fileUuid File UUID
     * @return string|array API response
     */
    public function deleteAppMedia(int|string $appId, string $version, string $fileUuid): string|array
    {
        return $this->delete($appId . '/' . $version . '/' . $fileUuid);
    }

    /**
     * Upload an app icon for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @param array $data Icon payload
     * @return string|array API response
     */
    public function uploadAppIcon(int|string $appId, string $version, array $data): string|array
    {
        return $this->post($appId . '/' . $version . '/icon', $data);
    }

    /**
     * Remove an app icon for an app version.
     *
     * @param int|string $appId App identifier
     * @param string $version App version string
     * @return string|array API response
     */
    public function removeAppIcon(int|string $appId, string $version): string|array
    {
        return $this->delete($appId . '/' . $version . '/icon');
    }
}
