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
    public function __construct($service, $name, $description, $status, $version, $patches, $dependencies) {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->service = $service;
        $this->version = $version;
        $this->patches = $patches;
        $this->dependencies = $dependencies;
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

    public function getDependencies() {
        return $this->dependencies;
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
        return $this->service->getApi()->dataDelete($url, array(
            "package" => $this->name
        ));
    }
    public function reinstall() {
        $url = "/services/".$this->service->getId()."/gameservers/packages/reinstall";
        return $this->service->getApi()->dataPut($url, array(
            "package" => $this->name
        ));
    }
}
