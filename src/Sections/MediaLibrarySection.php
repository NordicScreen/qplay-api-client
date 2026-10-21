<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Sections;

use QPlay\Api\Client\Exception\ApiException;

/**
 * Media library section supporting folders, fonts, and files.
 *
 * @namespace QPlay\Api\Client\Sections
 */
class MediaLibrarySection extends AbstractSection
{
    // ==========================================
    // Folders
    // ==========================================

    /**
     * List media folders.
     *
     * @param array $query Optional query parameters
     * @return string|array API response
     * @throws ApiException
     */
    public function listFolders(array $query = []): string|array
    {
        return $this->get('folder', $query);
    }

    /**
     * Get a media folder by id.
     *
     * @param int|string $folderId Folder identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function getFolder(int|string $folderId): string|array
    {
        return $this->get('folder/' . $folderId);
    }

    /**
     * Create a media folder.
     *
     * @param array $data Folder payload
     * @return string|array API response
     * @throws ApiException
     */
    public function createFolder(array $data): string|array
    {
        return $this->post('folder', $data);
    }

    /**
     * Update a media folder.
     *
     * @param int|string $folderId Folder identifier
     * @param array $data Fields to update
     * @return string|array API response
     * @throws ApiException
     */
    public function updateFolder(int|string $folderId, array $data): string|array
    {
        return $this->patch('folder/' . $folderId, $data);
    }

    /**
     * Delete a media folder by id.
     *
     * @param int|string $folderId Folder identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteFolder(int|string $folderId): string|array
    {
        return $this->delete('folder/' . $folderId);
    }

    // ==========================================
    // Fonts
    // ==========================================

    /**
     * List media fonts.
     *
     * @param array $query Optional query parameters
     * @return string|array API response
     * @throws ApiException
     */
    public function listFonts(array $query = []): string|array
    {
        return $this->get('font', $query);
    }

    /**
     * Download a font file.
     *
     * @param string $uuid Font UUID
     * @param int|string $id Font ID
     * @param string $filename Font filename
     * @return string|array API response
     * @throws ApiException
     */
    public function downloadFont(string $uuid, int|string $id, string $filename): string|array
    {
        return $this->get('font/' . $uuid . '/' . $id . '/download/' . $filename);
    }

    // ==========================================
    // Files
    // ==========================================

    /**
     * List media files.
     *
     * @param array $query Optional query parameters
     * @return string|array API response
     * @throws ApiException
     */
    public function listFiles(array $query = []): string|array
    {
        return $this->get('file', $query);
    }

    /**
     * Get a media file by id.
     *
     * @param int|string $fileId Media file identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function getFile(int|string $fileId): string|array
    {
        return $this->get('file/' . $fileId);
    }

    /**
     * Create/upload a media file.
     *
     * @param array $data Media file payload
     * @return string|array API response
     * @throws ApiException
     */
    public function createFile(array $data): string|array
    {
        return $this->post('file', $data);
    }

    /**
     * Delete a media file by id.
     *
     * @param int|string $fileId Media file identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteFile(int|string $fileId): string|array
    {
        return $this->delete('file/' . $fileId);
    }

    /**
     * Download a media file.
     *
     * @param int|string $fileId Media file identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function downloadFile(int|string $fileId): string|array
    {
        return $this->get('file/' . $fileId . '/download');
    }

    /**
     * Regenerate a media file.
     *
     * @param int|string $fileId Media file identifier
     * @param array $data Optional payload
     * @return string|array API response
     * @throws ApiException
     */
    public function regenerateFile(int|string $fileId, array $data = []): string|array
    {
        return $this->put('file/' . $fileId . '/regenerate', $data);
    }

    // ==========================================
    // Backward Compatibility Aliases (for files)
    // ==========================================

    /**
     * @alias listFiles
     *
     * @param array $query Optional query parameters
     * @return string|array API response
     * @throws ApiException
     */
    public function list(array $query = []): string|array
    {
        return $this->listFiles($query);
    }

    /**
     * @alias getFile
     *
     * @param int|string $mediaId Media file identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function getById(int|string $mediaId): string|array
    {
        return $this->getFile($mediaId);
    }

    /**
     * @alias createFile
     *
     * @param array $data Media file payload
     * @return string|array API response
     * @throws ApiException
     */
    public function create(array $data): string|array
    {
        return $this->createFile($data);
    }

    /**
     * @alias deleteFile
     *
     * @param int|string $mediaId Media file identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function deleteById(int|string $mediaId): string|array
    {
        return $this->deleteFile($mediaId);
    }

    /**
     * @alias downloadFile
     *
     * @param int|string $mediaId Media file identifier
     * @return string|array API response
     * @throws ApiException
     */
    public function download(int|string $mediaId): string|array
    {
        return $this->downloadFile($mediaId);
    }
}
