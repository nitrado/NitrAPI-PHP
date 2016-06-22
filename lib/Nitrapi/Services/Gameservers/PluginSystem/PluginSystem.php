<?php

namespace Nitrapi\Services\Gameservers\PluginSystem;

use Nitrapi\Services\Gameservers\Gameserver;

class PluginSystem
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    public function doInstall() {
        $url = "/services/".$this->service->getId()."/gameservers/plugin_system/install";
        $this->service->getApi()->dataPost($url);

        return true;
    }

    public function doUninstall() {
        $url = "/services/".$this->service->getId()."/gameservers/plugin_system/uninstall";
        $this->service->getApi()->dataDelete($url);

        return true;
    }

    public function doStop() {
        $url = "/services/".$this->service->getId()."/gameservers/plugin_system/stop";
        $this->service->getApi()->dataPost($url);

        return true;
    }

    public function doRestart() {
        $url = "/services/".$this->service->getId()."/gameservers/plugin_system/restart";
        $this->service->getApi()->dataPost($url);

        return true;
    }

    public function getInfo() {
        $url = "/services/".$this->service->getId()."/gameservers/plugin_system/info";
        $infos = $this->service->getApi()->dataGet($url);

        return $infos['plugin_system'];
    }
}