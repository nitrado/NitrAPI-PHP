<?php

namespace Nitrapi\Tests;

use Nitrapi\Nitrapi;
use Nitrapi\Services\CloudServers\CloudServer;
use Nitrapi\Tests\NitrapiTestCase;

class CloudServerTest extends NitrapiTestCase {
    private $images = null;
    
    protected function setUp(): void {
        $nitrapi = $this->nitrapiMock([
            'cloud_server.images' => []
        ]);
        $this->images = CloudServer::getAvailableImages($nitrapi);
    }

    function testImageObjectCreation() {
        $this->assertInstanceOf('Nitrapi\Services\CloudServers\Image', $this->images[0]);
    }

    function testImageAttributes() {
        $linux = $this->images[0];
        $this->assertEquals($linux->getId(), 1);
        $this->assertEquals($linux->getName(), "Ubuntu 16.04 LTS (Plain)");
        $this->assertFalse($linux->isWindows());
    }

    function testImageWindowsFlag() {
        $this->assertFalse($this->images[0]->isWindows());
        $this->assertTrue($this->images[1]->isWindows());
    }
}