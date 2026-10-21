<?php

declare(strict_types=1);

namespace QPlay\Api\Client;

use GuzzleHttp\Client as GuzzleHttpClient;
use Psr\Cache\CacheItemInterface;
use QPlay\Api\Client\Client as CoreClient;
use QPlay\Api\Client\Sections\AppSection;
use QPlay\Api\Client\Sections\AppTemplateSection;
use QPlay\Api\Client\Sections\Developer\AppSection as DevAppSection;
use QPlay\Api\Client\Sections\Developer\AppTemplateSection as DevAppTemplateSection;
use QPlay\Api\Client\Sections\Developer\IntegrationTemplateSection as DevIntegrationTemplateSection;
use QPlay\Api\Client\Sections\Developer\AppMediaSection as DevAppMediaSection;
use QPlay\Api\Client\Sections\Developer\AppTemplateMediaSection as DevAppTemplateMediaSection;
use QPlay\Api\Client\Sections\IntegrationSection;
use QPlay\Api\Client\Sections\IntegrationTemplateSection;
use QPlay\Api\Client\Sections\MediaLibrarySection;
use QPlay\Api\Client\Sections\PlayersSection;
use QPlay\Api\Client\Sections\PlaylistSection;
use QPlay\Api\Client\Sections\PresentationsSection;
use QPlay\Api\Client\Sections\SystemSection;

/**
 * @namespace QPlay\Api\Client
 */
class QPlayClient
{
    private CoreClient $core;

    /**
     * Simple façade over the internal core client providing a friendlier entrypoint and
     * direct accessors for each section handler.
     *
     * Example:
     * $api = new QPlayClient('https://host.tld/api/rest', $id, $secret);
     * $media = $api->media()->list();
     *
     * @param string $apiEndpoint The base URL of the Q-Play REST API
     * @param string $clientId OAuth2 client ID
     * @param string $clientSecret OAuth2 client secret
     * @param string[] $scopes OAuth2 scopes to request
     * @param CacheItemInterface|null $tokenCacheItem Optional PSR-6 cache item for token persistence
     * @param GuzzleHttpClient|null $httpClient Optional custom HTTP client for testing or customization
     */
    public function __construct(
        string $apiEndpoint,
        string $clientId,
        string $clientSecret,
        array $scopes = [],
        ?CacheItemInterface $tokenCacheItem = null,
        ?GuzzleHttpClient $httpClient = null,
    ) {
        $this->core = new CoreClient(
            apiEndpoint: $apiEndpoint,
            clientId: $clientId,
            clientSecret: $clientSecret,
            scopes: $scopes,
            tokenCacheItem: $tokenCacheItem,
            httpClient: $httpClient,
        );
    }

    /**
     * Access to the underlying core client if needed.
     *
     * @return CoreClient The core client instance
     */
    public function core(): CoreClient
    {
        return $this->core;
    }

    /**
     * Media Library API (/media)
     *
     * @return MediaLibrarySection The media library section handler
     */
    public function media(): MediaLibrarySection
    {
        return $this->core->media();
    }

    /**
     * Players API (/players)
     *
     * @return PlayersSection The players section handler
     */
    public function players(): PlayersSection
    {
        return $this->core->players();
    }

    /**
     * Presentations API (/presentation)
     *
     * @return PresentationsSection The presentations section handler
     */
    public function presentations(): PresentationsSection
    {
        return $this->core->presentations();
    }

    /**
     * Playlist API (/playlist)
     *
     * @return PlaylistSection The playlist section handler
     */
    public function playlist(): PlaylistSection
    {
        return $this->core->playlist();
    }

    /**
     * Integration API (/integration)
     *
     * @return IntegrationSection The integration section handler
     */
    public function integration(): IntegrationSection
    {
        return $this->core->integration();
    }

    /**
     * Integration Template API (/integration-template)
     *
     * @return IntegrationTemplateSection The integration template section handler
     */
    public function integrationTemplate(): IntegrationTemplateSection
    {
        return $this->core->integrationTemplate();
    }

    /**
     * Apps section accessor (/app) — read-only
     *
     * @return AppSection The app section handler
     */
    public function app(): AppSection
    {
        return $this->core->app();
    }

    /**
     * App Template section accessor (/app-template)
     *
     * @return AppTemplateSection The app template section handler
     */
    public function appTemplate(): AppTemplateSection
    {
        return $this->core->appTemplate();
    }

    /**
     * System API (/status, /health)
     *
     * @return SystemSection The system section handler
     */
    public function system(): SystemSection
    {
        return $this->core->system();
    }

    /**
     * Developer Apps section accessor (/developer/app)
     *
     * @return DevAppSection The developer app section handler
     */
    public function developerApp(): DevAppSection
    {
        return $this->core->developerApp();
    }

    /**
     * Developer App Template section accessor (/developer/app-template)
     *
     * @return DevAppTemplateSection The developer app template section handler
     */
    public function developerAppTemplate(): DevAppTemplateSection
    {
        return $this->core->developerAppTemplate();
    }

    /**
     * Developer Integration Template section accessor (/developer/integration-template)
     *
     * @return DevIntegrationTemplateSection The developer integration template section handler
     */
    public function developerIntegrationTemplate(): DevIntegrationTemplateSection
    {
        return $this->core->developerIntegrationTemplate();
    }

    /**
     * Developer App Media section accessor (/developer/app)
     *
     * @return DevAppMediaSection The developer app media section handler
     */
    public function developerAppMedia(): DevAppMediaSection
    {
        return $this->core->developerAppMedia();
    }

    /**
     * Developer App Template Media section accessor (/developer/app-template)
     *
     * @return DevAppTemplateMediaSection The developer app template media section handler
     */
    public function developerAppTemplateMedia(): DevAppTemplateMediaSection
    {
        return $this->core->developerAppTemplateMedia();
    }
}
