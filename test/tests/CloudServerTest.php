<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;
use Nitrapi\Services\CloudServers\CloudServer;

class CloudServerTest extends NitrapiTestCase {
    private $images = null;
    
    function setUp() {
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
        $this->assertEquals($linux->getId(), 2);
        $this->assertEquals($linux->getName(), "Ubuntu 14.04 LTS (Plain)");
        $this->assertFalse($linux->isWindows());
    }

    function testImageWindowsFlag() {
        $this->assertFalse($this->images[0]->isWindows());
        $this->assertTrue($this->images[1]->isWindows());
    }
}