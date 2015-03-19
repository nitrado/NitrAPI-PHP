<?php

namespace Nitrapi\Services\Gameservers\PluginSystem;

use Nitrapi\Services\Gameservers\Gameserver;

class PluginSystem
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver $service) {
        $this->service = $service;
    }

    public function doInstall() {
        try {
            $url = "/services/".$this->service->getId()."/gameservers/plugin_system/install";
            $this->service->getApi()->dataPost($url);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function doUninstall() {
        try {
            $url = "/services/".$this->service->getId()."/gameservers/plugin_system/uninstall";
            $this->service->getApi()->dataDelete($url);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function doStop() {
        try {
            $url = "/services/".$this->service->getId()."/gameservers/plugin_system/stop";
            $this->service->getApi()->dataPost($url);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function doRestart() {
        try {
            $url = "/services/".$this->service->getId()."/gameservers/plugin_system/restart";
            $this->service->getApi()->dataPost($url);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function getInfo() {
        $url = "/services/".$this->service->getId()."/gameservers/plugin_system/info";
        $infos = $this->service->getApi()->dataGet($url);

        return $infos['plugin_system'];
    }
}