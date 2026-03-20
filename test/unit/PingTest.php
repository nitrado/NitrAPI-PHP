<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;

class PingTest extends NitrapiTestCase {
    public function testPing(): void {
        $nitrapi = $this->nitrapiMock(['ping' => []]);

        $response = $nitrapi->dataGet('ping');
        $this->assertEquals('All systems operate as expected.', $response);
    }
}
