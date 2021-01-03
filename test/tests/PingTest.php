<?php

namespace Nitrapi\Tests;

use Nitrapi\Nitrapi;
use Nitrapi\Tests\NitrapiTestCase;

class PingTest extends NitrapiTestCase {
    function testPing() {
        $nitrapi = $this->nitrapiMock(['ping' => []]);

        $response = $nitrapi->dataGet('ping');
        $this->assertEquals($response, 'cloud1000.nitrado.cloud with version 1337 is running fine!');
    }
}