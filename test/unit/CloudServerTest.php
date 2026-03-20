<?php

namespace Nitrapi\Tests;

use Nitrapi\Tests\NitrapiTestCase;
use Nitrapi\Nitrapi;
use Nitrapi\Services\CloudServers\CloudServer;
use Nitrapi\Services\CloudServers\Image;

class CloudServerTest extends NitrapiTestCase {
    private $images = null;
    
    public function setUp(): void {
        $nitrapi = $this->nitrapiMock([
            'cloud_server.images' => []
        ]);
        $this->images = CloudServer::getAvailableImages($nitrapi);
    }

    public function testImageObjectCreation(): void {
        $this->assertInstanceOf(Image::class, $this->images[0]);
    }

    public function testImageAttributes(): void {
        $linux = $this->images[0];
        $this->assertEquals(2, $linux->getId());
        $this->assertEquals("Ubuntu 14.04 LTS (Plain)", $linux->getName());
        $this->assertFalse($linux->isWindows());
    }

    public function testImageWindowsFlag(): void {
        $this->assertFalse($this->images[0]->isWindows());
        $this->assertTrue($this->images[1]->isWindows());
    }
}
