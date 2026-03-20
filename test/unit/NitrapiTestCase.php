<?php

namespace Nitrapi\Tests;

use Http\Mock\Client as MockClient;
use Nitrapi\Nitrapi;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

abstract class NitrapiTestCase extends TestCase
{
    /**
     * Creates a Nitrapi instance backed by a mock HTTP client.
     *
     * @param array<string, array> $fixtures  Map of fixture-name => extra headers,
     *                                         e.g. ['user' => [], 'ping' => []]
     *                                         Responses are queued in iteration order.
     */
    protected function nitrapiMock(array $fixtures): Nitrapi
    {
        $factory    = new Psr17Factory();
        $mockClient = new MockClient($factory);

        foreach ($fixtures as $name => $headers) {
            $body = file_get_contents(__DIR__ . '/../fixtures/' . $name . '.response');
            $mockClient->addResponse(new Response(200, $headers, $body));
        }

        return new Nitrapi(null, [
            'http_client'     => $mockClient,
            'request_factory' => $factory,
            'stream_factory'  => $factory,
        ]);
    }
}
