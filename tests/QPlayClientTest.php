<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Tests;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use QPlay\Api\Client\QPlayClient;
use QPlay\Api\Client\Sections\AppSection;
use QPlay\Api\Client\Sections\AppTemplateSection;
use QPlay\Api\Client\Sections\Developer\AppSection as DevAppSection;
use QPlay\Api\Client\Sections\Developer\AppTemplateSection as DevAppTemplateSection;
use QPlay\Api\Client\Sections\Developer\IntegrationTemplateSection as DevIntegrationTemplateSection;
use QPlay\Api\Client\Sections\Developer\MediaSection as DevMediaSection;
use QPlay\Api\Client\Sections\EventsSection;
use QPlay\Api\Client\Sections\IntegrationSection;
use QPlay\Api\Client\Sections\IntegrationTemplateSection;
use QPlay\Api\Client\Sections\MediaLibrarySection;
use QPlay\Api\Client\Sections\PlayersSection;
use QPlay\Api\Client\Sections\PlaylistSection;
use QPlay\Api\Client\Sections\PresentationsSection;
use QPlay\Api\Client\Sections\ServerSection;
use QPlay\Api\Client\Sections\SystemSection;

class QPlayClientTest extends TestCase
{
    public function testFaçadeAccessors(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => 'tok', 'expires_in' => 3600])),
            new Response(200, [], json_encode(['status' => 'ok'])),
        ]);
        $history = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));
        $httpClient = new GuzzleHttpClient(['handler' => $handlerStack]);

        $api = new QPlayClient(
            apiEndpoint: 'https://api.test/api/rest/v2',
            clientId: 'id',
            clientSecret: 'secret',
            httpClient: $httpClient
        );

        $this->assertInstanceOf(MediaLibrarySection::class, $api->media());
        $this->assertInstanceOf(PlayersSection::class, $api->players());
        $this->assertInstanceOf(PresentationsSection::class, $api->presentations());
        $this->assertInstanceOf(PlaylistSection::class, $api->playlist());
        $this->assertInstanceOf(EventsSection::class, $api->events());
        $this->assertInstanceOf(IntegrationSection::class, $api->integration());
        $this->assertInstanceOf(IntegrationTemplateSection::class, $api->integrationTemplate());
        $this->assertInstanceOf(AppSection::class, $api->app());
        $this->assertInstanceOf(AppTemplateSection::class, $api->appTemplate());
        $this->assertInstanceOf(SystemSection::class, $api->system());
        $this->assertInstanceOf(ServerSection::class, $api->server());
        $this->assertInstanceOf(DevAppSection::class, $api->developerApp());
        $this->assertInstanceOf(DevAppTemplateSection::class, $api->developerAppTemplate());
        $this->assertInstanceOf(DevIntegrationTemplateSection::class, $api->developerIntegrationTemplate());
        $this->assertInstanceOf(DevMediaSection::class, $api->developerMedia());

        $this->assertEquals(['status' => 'ok'], $api->system()->health());
        $this->assertEquals('https://api.test/api/rest/v2/health', (string)$history[1]['request']->getUri());
    }
}
