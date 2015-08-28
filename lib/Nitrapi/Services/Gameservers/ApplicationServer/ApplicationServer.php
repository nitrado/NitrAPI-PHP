<?php

namespace Nitrapi\Services\Gameservers\ApplicationServer;

use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Services\Gameservers\Gameserver;

class ApplicationServer
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    /**
     * Sends a ping to the application server
     *
     * @return bool
     */
    public function ping() {
        try {
            $url = "/services/".$this->service->getId()."/gameservers/app_server";
            $this->service->getApi()->dataGet($url);
            return true;
        } catch (\Exception $e) {}

        return false;
    }

    /**
     * Sends a command to the app server
     *
     * @param $command string
     * @return bool
     */
    public function sendCommand($command) {
        $url = "/services/".$this->service->getId()."/gameservers/app_server/command";
        $this->service->getApi()->dataPost($url, array(
            "command" => $command
        ));
        return true;
    }
}