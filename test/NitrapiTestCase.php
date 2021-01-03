<?php

namespace Nitrapi\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Nitrapi\Nitrapi;

abstract class NitrapiTestCase extends \PHPUnit\Framework\TestCase {
    function nitrapiMock($urls) {
        $responses = [];
        foreach ($urls as $url => $params)
            $responses[] = new Response(200, $params, file_get_contents(__DIR__ . '/fixtures/' . $url . '.response'));
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        return new Nitrapi(null, ['handler' => $handler], null);
    }
}
