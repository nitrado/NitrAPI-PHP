<?php

namespace Nitrapi\Tests;

use Nitrapi\Nitrapi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

abstract class NitrapiTestCase extends \PHPUnit_Framework_TestCase {
    function nitrapiMock($urls) {
        $responses = [];
        foreach ($urls as $url => $params)
            $responses[] = new Response(200, $params, file_get_contents(__DIR__ . '/fixtures/' . $url . '.response'));
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        return new Nitrapi(null, ['handler' => $handler], null);
    }
}
