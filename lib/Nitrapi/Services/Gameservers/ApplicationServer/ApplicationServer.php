<?php

namespace Nitrapi\Services\Gameservers\ApplicationServer;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Gameservers\Gameserver;

class ApplicationServer
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver $service)
    {
        $this->service = $service;
    }

    /**
     * Sends a ping to the application server
     *
     * @return bool
     */
    public function ping(): bool
    {
        try {
            $url = "/services/" . $this->service->getId() . "/gameservers/app_server";
            $this->service->getApi()->dataGet($url);
            return true;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Sends a command to the app server
     *
     * @param string $command
     * @return bool
     * @throws NitrapiException
     */
    public function sendCommand(string $command): bool
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/app_server/command";
        $this->service->getApi()->dataPost($url, [
            "command" => $command,
        ]);
        return true;
    }
}
