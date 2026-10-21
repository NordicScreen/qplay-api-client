<?php

declare(strict_types=1);

namespace QPlay\Api\Client\Tests;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use QPlay\Api\Client\Client;
use QPlay\Api\Client\Exception\BadRequestException;
use QPlay\Api\Client\Exception\ForbiddenException;
use QPlay\Api\Client\Exception\MethodNotAllowedException;
use QPlay\Api\Client\Exception\NotFoundException;
use QPlay\Api\Client\Exception\ServerErrorException;
use QPlay\Api\Client\Exception\TooManyRequestsException;
use QPlay\Api\Client\Exception\UnauthorizedException;
use QPlay\Api\Client\Exception\ValidationException;

class ClientTest extends TestCase
{
    private function createClientWithMockResponses(
        array $responses,
        array &$history = [],
        ?CacheItemInterface $cacheItem = null
    ): Client {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));
        $httpClient = new GuzzleHttpClient(['handler' => $handlerStack]);

        return new Client(
            apiEndpoint: 'https://api.test/api/rest/v2',
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            scopes: ['api:read', 'api:write'],
            tokenCacheItem: $cacheItem,
            httpClient: $httpClient
        );
    }

    public function testSuccessfulRequestFetchesTokenAndSendsBearer(): void
    {
        $history = [];
        $tokenResponse = new Response(200, [], json_encode([
            'access_token' => 'mock-access-token',
            'expires_in' => 3600,
        ]));
        $apiResponse = new Response(200, [], json_encode([
            'data' => ['id' => 1, 'name' => 'Test']
        ]));

        $client = $this->createClientWithMockResponses([$tokenResponse, $apiResponse], $history);
        $result = $client->get('test-endpoint');

        $this->assertIsArray($result);
        $this->assertEquals(['data' => ['id' => 1, 'name' => 'Test']], $result);

        $this->assertCount(2, $history);

        // 1. Token Request
        /** @var Request $tokenRequest */
        $tokenRequest = $history[0]['request'];
        $this->assertEquals('POST', $tokenRequest->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/oauth/token', (string)$tokenRequest->getUri());
        $tokenBody = (string)$tokenRequest->getBody();
        $this->assertStringContainsString('grant_type=client_credentials', $tokenBody);
        $this->assertStringContainsString('client_id=test-client-id', $tokenBody);
        $this->assertStringContainsString('client_secret=test-client-secret', $tokenBody);

        // 2. API Request
        /** @var Request $apiRequest */
        $apiRequest = $history[1]['request'];
        $this->assertEquals('GET', $apiRequest->getMethod());
        $this->assertEquals('https://api.test/api/rest/v2/test-endpoint', (string)$apiRequest->getUri());
        $this->assertEquals(['Bearer mock-access-token'], $apiRequest->getHeader('Authorization'));
    }

    public function testTokenExpiryAnd401Retry(): void
    {
        $history = [];
        $firstTokenResponse = new Response(200, [], json_encode([
            'access_token' => 'token-1',
            'expires_in' => 3600,
        ]));
        $unauthorizedResponse = new Response(401, [], json_encode([
            'meta' => ['error' => 'Token expired']
        ]));
        $secondTokenResponse = new Response(200, [], json_encode([
            'access_token' => 'token-2',
            'expires_in' => 3600,
        ]));
        $successResponse = new Response(200, [], json_encode([
            'status' => 'ok'
        ]));

        $client = $this->createClientWithMockResponses([
            $firstTokenResponse,
            $unauthorizedResponse,
            $secondTokenResponse,
            $successResponse,
        ], $history);

        $res = $client->get('protected');
        $this->assertEquals(['status' => 'ok'], $res);
        $this->assertCount(4, $history);
        $this->assertEquals('Bearer token-1', $history[1]['request']->getHeaderLine('Authorization'));
        $this->assertEquals('Bearer token-2', $history[3]['request']->getHeaderLine('Authorization'));
    }

    public function testTokenCacheItemUsage(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(true);
        $cacheItem->method('get')->willReturn([
            'token' => 'cached-token-123',
            'expiresAt' => time() + 1000,
        ]);

        $history = [];
        $apiResponse = new Response(200, [], json_encode(['cached' => true]));

        $client = $this->createClientWithMockResponses([$apiResponse], $history, $cacheItem);
        $res = $client->get('cached-route');

        $this->assertEquals(['cached' => true], $res);
        $this->assertCount(1, $history);
        $this->assertEquals('Bearer cached-token-123', $history[0]['request']->getHeaderLine('Authorization'));
    }

    public function testExceptionMapping(): void
    {
        $cases = [
            [400, BadRequestException::class],
            [401, UnauthorizedException::class],
            [403, ForbiddenException::class],
            [404, NotFoundException::class],
            [405, MethodNotAllowedException::class],
            [422, ValidationException::class],
            [429, TooManyRequestsException::class],
            [500, ServerErrorException::class],
        ];

        foreach ($cases as [$statusCode, $expectedExceptionClass]) {
            $tokenResponse = new Response(200, [], json_encode([
                'access_token' => 'mock-token',
                'expires_in' => 3600,
            ]));
            $errorResponse = new Response($statusCode, [], json_encode([
                'meta' => [
                    'error' => 'Error for ' . $statusCode,
                    'details' => ['field' => 'invalid'],
                ]
            ]));

            // On 401 it will retry once, so provide token + error + token + error
            $responses = ($statusCode === 401)
                ? [$tokenResponse, $errorResponse, $tokenResponse, $errorResponse]
                : [$tokenResponse, $errorResponse];

            $client = $this->createClientWithMockResponses($responses);

            try {
                $client->get('error-test');
                $this->fail("Expected exception $expectedExceptionClass was not thrown for status $statusCode");
            } catch (\Throwable $e) {
                $this->assertInstanceOf($expectedExceptionClass, $e);
                $this->assertEquals('Error for ' . $statusCode, $e->getMessage());
                $this->assertEquals($statusCode, $e->getCode());
            }
        }
    }

    public function testHttpVerbs(): void
    {
        $history = [];
        $tokenResponse = new Response(200, [], json_encode(['access_token' => 'tok', 'expires_in' => 3600]));
        $postResp = new Response(201, [], json_encode(['method' => 'POST']));
        $putResp = new Response(200, [], json_encode(['method' => 'PUT']));
        $patchResp = new Response(200, [], json_encode(['method' => 'PATCH']));
        $deleteResp = new Response(200, [], json_encode(['method' => 'DELETE']));

        $client = $this->createClientWithMockResponses([
            $tokenResponse,
            $postResp,
            $putResp,
            $patchResp,
            $deleteResp,
        ], $history);

        $client->post('post-route', ['foo' => 'bar']);
        $client->put('put-route', ['foo' => 'bar']);
        $client->patch('patch-route', ['foo' => 'bar']);
        $client->delete('delete-route', ['id' => 5]);

        $this->assertEquals('POST', $history[1]['request']->getMethod());
        $this->assertEquals('PUT', $history[2]['request']->getMethod());
        $this->assertEquals('PATCH', $history[3]['request']->getMethod());
        $this->assertEquals('DELETE', $history[4]['request']->getMethod());
    }
}
