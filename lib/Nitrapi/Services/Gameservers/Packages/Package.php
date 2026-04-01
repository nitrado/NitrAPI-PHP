<?php

namespace Nitrapi\Services\Gameservers\Packages;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Service;

class Package
{
    /**
     * @var mixed
     */
    private $name;
    /**
     * @var mixed
     */
    private $description;
    /**
     * @var mixed
     */
    private $status;
    /**
     * @var Service
     */
    private $service;
    /**
     * @var mixed
     */
    private $version;
    /**
     * @var mixed
     */
    private $patches;
    /**
     * @var mixed
     */
    private $dependencies;

    /**
     * Package constructor.
     * @param Service $service
     * @param $name
     * @param $description
     * @param $status
     * @param $version
     * @param $patches
     * @param $dependencies
     */
    public function __construct(Service $service, $name, $description, $status, $version, $patches, $dependencies)
    {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->service = $service;
        $this->version = $version;
        $this->patches = $patches;
        $this->dependencies = $dependencies;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getPatches()
    {
        return $this->patches;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @throws NitrapiException
     */
    public function install($version)
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/packages/install";
        return $this->service->getApi()->dataPost($url, [
            "package" => $this->name,
            "version" => $version,
        ]);
    }

    /**
     * @throws NitrapiException
     */
    public function uninstall()
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/packages/uninstall";
        return $this->service->getApi()->dataDelete($url, [
            "package" => $this->name,
        ]);
    }

    /**
     * @throws NitrapiException
     */
    public function reinstall()
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/packages/reinstall";
        return $this->service->getApi()->dataPut($url, [
            "package" => $this->name,
        ]);
    }
}
