<?php

namespace Nitrapi\Services\Gameservers\Packages;

use Nitrapi\Common\Exceptions\NitrapiErrorException;

class Package {
    /**
     * Package constructor.
     * @param Service $serviced
     * @param Package $name
     * @param Description $description
     * @param Status $status
     */
    public function __construct($service, $name, $description, $status, $version, $patches) {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->service = $service;
        $this->version = $version;
        $this->patches = $patches;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getPatches() {
        return $this->patches;
    }

    public function install($version) {
        $url = "/services/".$this->service->getId()."/gameservers/packages/install";
        return $this->service->getApi()->dataPost($url, array(
            "package" => $this->name,
            "version" => $version
        ));
    }
    public function uninstall() {
        $url = "/services/".$this->service->getId()."/gameservers/packages/uninstall";
        return $this->service->getApi()->dataPost($url, array(
            "package" => $this->name
        ));
    }
    public function reinstall() {
        $url = "/services/".$this->service->getId()."/gameservers/packages/reinstall";
        return $this->service->getApi()->dataPost($url, array(
            "package" => $this->name
        ));
    }
}
