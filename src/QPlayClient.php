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
use QPlay\Api\Client\Sections\EventsSection;
use QPlay\Api\Client\Sections\IntegrationSection;
use QPlay\Api\Client\Sections\IntegrationTemplateSection;
use QPlay\Api\Client\Sections\MediaLibrarySection;
use QPlay\Api\Client\Sections\PlayersSection;
use QPlay\Api\Client\Sections\PresentationsSection;
use QPlay\Api\Client\Sections\ServerSection;
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
     */
    public function core(): CoreClient
    {
        return $this->core;
    }

    /**
     * Media Library API (/media)
     */
    public function media(): MediaLibrarySection
    {
        return $this->core->media();
    }

    /**
     * Players API (/players)
     */
    public function players(): PlayersSection
    {
        return $this->core->players();
    }

    /**
     * Presentations API (/presentations)
     */
    public function presentations(): PresentationsSection
    {
        return $this->core->presentations();
    }

    /**
     * Events API (/events)
     */
    public function events(): EventsSection
    {
        return $this->core->events();
    }

    /**
     * Integration API (/integration)
     */
    public function integration(): IntegrationSection
    {
        return $this->core->integration();
    }

    /**
     * Integration Template API (/integration-template)
     */
    public function integrationTemplate(): IntegrationTemplateSection
    {
        return $this->core->integrationTemplate();
    }

    /**
     * Apps section accessor (/apps) — read-only
     */
    public function app(): AppSection
    {
        return $this->core->app();
    }

    /**
     * App Template section accessor (/app-template) — read-only
     */
    public function appTemplate(): AppTemplateSection
    {
        return $this->core->appTemplate();
    }

    /**
     * System API (/system)
     */
    public function system(): SystemSection
    {
        return $this->core->system();
    }

    /**
     * Server API (/server)
     */
    public function server(): ServerSection
    {
        return $this->core->server();
    }

    /**
     * Developer Apps section accessor (/developer/apps)
     */
    public function developerApp(): DevAppSection
    {
        return $this->core->developerApp();
    }

    /**
     * Developer App Template section accessor (/developer/app-template)
     */
    public function developerAppTemplate(): DevAppTemplateSection
    {
        return $this->core->developerAppTemplate();
    }

    /**
     * Developer Integration Template section accessor (/developer/integration-template)
     */
    public function developerIntegrationTemplate(): DevIntegrationTemplateSection
    {
        return $this->core->developerIntegrationTemplate();
    }
}
