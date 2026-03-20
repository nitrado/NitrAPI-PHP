<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;

class GameserverLogsTest extends NitrapiTestCase {
    public function testAllGameserverLogAttributes(): void {
        $nitrapi = $this->nitrapiMock([
            'service3' => [],
            'gameservers' => [],
            'gameservers.logs' => []
        ]);

        $service = $nitrapi->getService(['id' => 3]);
        $logfiles = $service->getLogs();

        $this->assertEquals(1, $logfiles['current_page']);
        $this->assertEquals(40, $logfiles['logs_per_page']);
        $this->assertIsArray($logfiles['logs']);
        $this->assertIsArray($logfiles['logs'][0]);
        $this->assertEquals('Tyrola', $logfiles['logs'][0]['user']);
    }
}
