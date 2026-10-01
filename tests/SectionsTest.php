<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Tests;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use QPlay\Api\Client\Client;

class SectionsTest extends TestCase
{
    private function createClient(array $responses, array &$history): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));
        $httpClient = new GuzzleHttpClient(['handler' => $handlerStack]);

        return new Client(
            apiEndpoint: 'https://api.test/api/rest/v2',
            clientId: 'id',
            clientSecret: 'secret',
            httpClient: $httpClient
        );
    }

    private function tokenResponse(): Response
    {
        return new Response(200, [], json_encode(['access_token' => 'tok', 'expires_in' => 3600]));
    }

    private function okResponse(array $data = ['status' => 'ok']): Response
    {
        return new Response(200, [], json_encode($data));
    }

    public function testSystemSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $client->system()->status();
        $client->system()->health();

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/status', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/health', (string)$history[2]['request']->getUri());
    }

    public function testPlayersSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $players = $client->players();
        $players->list(['page' => 1]);
        $players->getById(10);
        $players->create(['name' => 'Player 1']);
        $players->update(10, ['name' => 'Updated']);
        $players->deleteById(10);
        $players->getStatus(10);
        $players->getScreenshot(10);
        $players->getBlackscreenSchedule(10);
        $players->sendCommand(10, ['cmd' => 'reboot']);
        $players->unlinkPresentation(10, 5);
        $players->unlinkEvent(10, 7);

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players?page=1', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players', (string)$history[3]['request']->getUri());

        $this->assertEquals('PATCH', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10', (string)$history[5]['request']->getUri());

        $this->assertEquals('GET', $history[6]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10/status', (string)$history[6]['request']->getUri());

        $this->assertEquals('GET', $history[7]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10/screenshot', (string)$history[7]['request']->getUri());

        $this->assertEquals('GET', $history[8]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10/blackscreen-schedule', (string)$history[8]['request']->getUri());

        $this->assertEquals('POST', $history[9]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10/commands', (string)$history[9]['request']->getUri());

        $this->assertEquals('DELETE', $history[10]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10/presentations/5', (string)$history[10]['request']->getUri());

        $this->assertEquals('DELETE', $history[11]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/players/10/events/7', (string)$history[11]['request']->getUri());
    }

    public function testPresentationsSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $pres = $client->presentations();
        $pres->list();
        $pres->getById(1);
        $pres->create(['title' => 'Pres']);
        $pres->update(1, ['title' => 'Updated']);
        $pres->deleteById(1);

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/presentation', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/presentation/1', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/presentation', (string)$history[3]['request']->getUri());

        $this->assertEquals('PATCH', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/presentation/1', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/presentation/1', (string)$history[5]['request']->getUri());
    }

    public function testPlaylistSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $pls = $client->playlist();
        $pls->list();
        $pls->getById(1);
        $pls->create(['name' => 'Playlist']);
        $pls->update(1, ['name' => 'Updated']);
        $pls->deleteById(1);

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/playlist', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/playlist/1', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/playlist', (string)$history[3]['request']->getUri());

        $this->assertEquals('PATCH', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/playlist/1', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/playlist/1', (string)$history[5]['request']->getUri());
    }

    public function testMediaLibrarySection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $media = $client->media();

        // Folders
        $media->listFolders();
        $media->getFolder(1);
        $media->createFolder(['name' => 'Folder 1']);
        $media->updateFolder(1, ['name' => 'Updated']);
        $media->deleteFolder(1);

        // Fonts
        $media->listFonts();
        $media->downloadFont('uuid-123', 5, 'Roboto.ttf');

        // Files
        $media->listFiles();
        $media->getFile('file-1');
        $media->createFile(['file' => 'data']);
        $media->deleteFile('file-1');
        $media->downloadFile('file-1');
        $media->regenerateFile('file-1');

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/folder', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/folder/1', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/folder', (string)$history[3]['request']->getUri());

        $this->assertEquals('PATCH', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/folder/1', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/folder/1', (string)$history[5]['request']->getUri());

        $this->assertEquals('GET', $history[6]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/font', (string)$history[6]['request']->getUri());

        $this->assertEquals('GET', $history[7]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/font/uuid-123/5/download/Roboto.ttf', (string)$history[7]['request']->getUri());

        $this->assertEquals('GET', $history[8]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/file', (string)$history[8]['request']->getUri());

        $this->assertEquals('GET', $history[9]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/file/file-1', (string)$history[9]['request']->getUri());

        $this->assertEquals('POST', $history[10]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/file', (string)$history[10]['request']->getUri());

        $this->assertEquals('DELETE', $history[11]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/file/file-1', (string)$history[11]['request']->getUri());

        $this->assertEquals('GET', $history[12]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/file/file-1/download', (string)$history[12]['request']->getUri());

        $this->assertEquals('PUT', $history[13]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/media/file/file-1/regenerate', (string)$history[13]['request']->getUri());
    }

    public function testAppSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $app = $client->app();
        $app->list();
        $app->getById('my-app');
        $app->listVersions('my-app');
        $app->getVersion('my-app', '1', 0);

        $this->assertEquals('https://api.test/api/rest/v2/app', (string)$history[1]['request']->getUri());
        $this->assertEquals('https://api.test/api/rest/v2/app/my-app', (string)$history[2]['request']->getUri());
        $this->assertEquals('https://api.test/api/rest/v2/app/my-app/version', (string)$history[3]['request']->getUri());
        $this->assertEquals('https://api.test/api/rest/v2/app/my-app/version/1.0', (string)$history[4]['request']->getUri());
    }

    public function testAppTemplateSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $template = $client->appTemplate();
        $template->list();
        $template->getById(1);
        $template->create(['name' => 'Tpl']);
        $template->update(1, ['name' => 'Updated']);
        $template->deleteById(1);
        $template->listVersions(1);
        $template->getVersion(1, 1, 0);
        $template->createVersion(1, ['code' => 'abc']);
        $template->updateVersion(1, 1, 0, ['code' => 'xyz']);
        $template->deleteVersion(1, 1, 0);

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template', (string)$history[3]['request']->getUri());

        $this->assertEquals('PUT', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1', (string)$history[5]['request']->getUri());

        $this->assertEquals('GET', $history[6]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1/version', (string)$history[6]['request']->getUri());

        $this->assertEquals('GET', $history[7]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1/version/1.0', (string)$history[7]['request']->getUri());

        $this->assertEquals('POST', $history[8]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1/version', (string)$history[8]['request']->getUri());

        $this->assertEquals('PUT', $history[9]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1/version/1.0', (string)$history[9]['request']->getUri());

        $this->assertEquals('DELETE', $history[10]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/app-template/1/version/1.0', (string)$history[10]['request']->getUri());
    }

    public function testIntegrationSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $integration = $client->integration();
        $integration->list();
        $integration->getById(1);
        $integration->create(['name' => 'Int']);
        $integration->update(1, ['name' => 'Int 2']);
        $integration->deleteById(1);
        $integration->run(1, ['force' => true]);
        $integration->statistics(1, ['days' => 7]);

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration/1', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration', (string)$history[3]['request']->getUri());

        $this->assertEquals('PUT', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration/1', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration/1', (string)$history[5]['request']->getUri());

        $this->assertEquals('POST', $history[6]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration/1/run', (string)$history[6]['request']->getUri());

        $this->assertEquals('GET', $history[7]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration/1/statistics?days=7', (string)$history[7]['request']->getUri());
    }

    public function testIntegrationTemplateSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $it = $client->integrationTemplate();
        $it->list();
        $it->getById(1);
        $it->create(['name' => 'IT']);
        $it->update(1, ['name' => 'IT 2']);
        $it->deleteById(1);
        $it->listVersions(1);
        $it->getVersion(1, 2);
        $it->createVersion(1, ['version' => 3]);
        $it->updateVersion(1, 2, ['version' => 2]);
        $it->deleteVersion(1, 2);
        $it->listScripts(1, 2);
        $it->getScript(1, 2, 3);
        $it->createScript(1, 2, ['script' => 'code']);
        $it->updateScript(1, 2, 3, ['script' => 'updated']);
        $it->deleteScript(1, 2, 3);

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template', (string)$history[3]['request']->getUri());

        $this->assertEquals('PUT', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1', (string)$history[5]['request']->getUri());

        $this->assertEquals('GET', $history[6]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version', (string)$history[6]['request']->getUri());

        $this->assertEquals('GET', $history[7]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2', (string)$history[7]['request']->getUri());

        $this->assertEquals('POST', $history[8]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version', (string)$history[8]['request']->getUri());

        $this->assertEquals('PUT', $history[9]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2', (string)$history[9]['request']->getUri());

        $this->assertEquals('DELETE', $history[10]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2', (string)$history[10]['request']->getUri());

        $this->assertEquals('GET', $history[11]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2/script', (string)$history[11]['request']->getUri());

        $this->assertEquals('GET', $history[12]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2/script/3', (string)$history[12]['request']->getUri());

        $this->assertEquals('POST', $history[13]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2/script', (string)$history[13]['request']->getUri());

        $this->assertEquals('PUT', $history[14]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2/script/3', (string)$history[14]['request']->getUri());

        $this->assertEquals('DELETE', $history[15]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/integration-template/1/version/2/script/3', (string)$history[15]['request']->getUri());
    }

    public function testEventsSection(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        $events = $client->events();
        $events->list();
        $events->getById(1);
        $events->create(['name' => 'Event']);
        $events->update(1, ['name' => 'Event 2']);
        $events->deleteById(1);

        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/events', (string)$history[1]['request']->getUri());

        $this->assertEquals('GET', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/events/1', (string)$history[2]['request']->getUri());

        $this->assertEquals('POST', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/events', (string)$history[3]['request']->getUri());

        $this->assertEquals('PUT', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/events/1', (string)$history[4]['request']->getUri());

        $this->assertEquals('DELETE', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/events/1', (string)$history[5]['request']->getUri());
    }

    public function testDeveloperSections(): void
    {
        $history = [];
        $client = $this->createClient([
            $this->tokenResponse(),
            // dev app
            $this->okResponse(),
            $this->okResponse(),
            // dev app template
            $this->okResponse(),
            $this->okResponse(),
            // dev integration template
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            // dev media
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
            $this->okResponse(),
        ], $history);

        // Dev App
        $client->developerApp()->update('my-app', ['title' => 'Updated']);
        $client->developerApp()->updateVersion('my-app', 1, 0, ['patch' => true]);

        $this->assertEquals('PUT', $history[1]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/app/my-app', (string)$history[1]['request']->getUri());
        $this->assertEquals('PUT', $history[2]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/app/my-app/version/1.0', (string)$history[2]['request']->getUri());

        // Dev App Template
        $client->developerAppTemplate()->update(1, ['name' => 'Updated']);
        $client->developerAppTemplate()->updateVersion(1, 1, 0, ['data' => 'x']);

        $this->assertEquals('PUT', $history[3]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/app-template/1', (string)$history[3]['request']->getUri());
        $this->assertEquals('PUT', $history[4]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/app-template/1/version/1.0', (string)$history[4]['request']->getUri());

        // Dev Integration Template
        $client->developerIntegrationTemplate()->update(1, ['name' => 'Updated']);
        $client->developerIntegrationTemplate()->updateVersion(1, 2, ['version' => '2']);
        $client->developerIntegrationTemplate()->updateScript(1, 2, 3, ['code' => 'eval']);

        $this->assertEquals('PUT', $history[5]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/integration-template/1', (string)$history[5]['request']->getUri());
        $this->assertEquals('PUT', $history[6]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/integration-template/1/version/2', (string)$history[6]['request']->getUri());
        $this->assertEquals('PUT', $history[7]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/integration-template/1/version/2/script/3', (string)$history[7]['request']->getUri());

        // Dev Media
        $devMedia = $client->developerMedia();
        $devMedia->getTemplateMedia(1, '1.0.0');
        $devMedia->uploadTemplateMedia(1, '1.0.0', ['file' => 'binary']);
        $devMedia->deleteTemplateMedia(1, '1.0.0', 'uuid-file');
        $devMedia->getAppMedia('my-app', '1.0.0');
        $devMedia->uploadAppMedia('my-app', '1.0.0', ['file' => 'binary']);
        $devMedia->deleteAppMedia('my-app', '1.0.0', 'uuid-file');
        $devMedia->uploadAppIcon('my-app', '1.0.0', ['icon' => 'png']);
        $devMedia->removeAppIcon('my-app', '1.0.0');

        $this->assertEquals('GET', $history[8]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/template/1/1.0.0', (string)$history[8]['request']->getUri());

        $this->assertEquals('POST', $history[9]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/template/1/1.0.0', (string)$history[9]['request']->getUri());

        $this->assertEquals('DELETE', $history[10]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/template/1/1.0.0/uuid-file', (string)$history[10]['request']->getUri());

        $this->assertEquals('GET', $history[11]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/my-app/1.0.0', (string)$history[11]['request']->getUri());

        $this->assertEquals('POST', $history[12]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/my-app/1.0.0', (string)$history[12]['request']->getUri());

        $this->assertEquals('DELETE', $history[13]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/my-app/1.0.0/uuid-file', (string)$history[13]['request']->getUri());

        $this->assertEquals('POST', $history[14]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/my-app/1.0.0/icon', (string)$history[14]['request']->getUri());

        $this->assertEquals('DELETE', $history[15]['request']->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/developer/media/my-app/1.0.0/icon', (string)$history[15]['request']->getUri());
    }
}
