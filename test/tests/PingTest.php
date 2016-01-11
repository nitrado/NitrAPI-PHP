<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;

class PingTest extends NitrapiTestCase {
    function testPing() {
        $nitrapi = $this->nitrapiMock(['ping' => []]);

        $response = $nitrapi->dataGet('ping');
        $this->assertEquals($response, 'All systems operate as expected.');
    }
}