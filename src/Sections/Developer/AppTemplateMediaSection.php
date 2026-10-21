<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections\Developer;

use QPlay\Api\Client\Exception\ApiException;
use QPlay\Api\Client\Sections\AbstractSection;

/**
 * Developer app template media section for template media assets.
 *
 * @namespace QPlay\Api\Client\Sections\Developer
 */
class AppTemplateMediaSection extends AbstractSection
{
    /**
     * Get media assets for an app template version.
     *
     * @param int|string $templateId Template identifier
     * @param string $version Template version string
     * @return string|array API response
     * @throws ApiException
     */
    public function getMedia(int|string $templateId, string $version): string|array
    {
        return $this->get($templateId . '/version/' . $version . '/media');
    }

    /**
     * Upload a media asset for an app template version.
     *
     * @param int|string $templateId Template identifier
     * @param string $version Template version string
     * @param array $data Upload payload
     * @return string|array API response
     * @throws ApiException
     */
    public function uploadMedia(int|string $templateId, string $version, array $data): string|array
    {
        return $this->post($templateId . '/version/' . $version . '/media', $data);
    }

    /**
     * Delete a media asset for an app template version.
     *
     * @param int|string $templateId Template identifier
     * @param string $version Template version string
     * @param string $fileUuid File UUID
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteMedia(int|string $templateId, string $version, string $fileUuid): string|array
    {
        return $this->delete($templateId . '/version/' . $version . '/media/' . $fileUuid);
    }
}
