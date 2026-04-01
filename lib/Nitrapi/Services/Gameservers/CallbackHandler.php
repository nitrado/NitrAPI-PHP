<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Common\Exceptions\NitrapiException;

class CallbackHandler
{
    protected $service;

    public function __construct(Gameserver $service)
    {
        $this->service = $service;
    }

    public function getService(): Gameserver
    {
        return $this->service;
    }

    /**
     * Trigger if a game has been installed
     * @param string $gameShort
     * @return bool
     * @throws NitrapiException
     */
    public function installed(string $gameShort): bool
    {
        $url = "/services/" . $this->getService()->getId() . "/gameservers/callback/installed";
        $this->getService()->getApi()->dataPost($url, [
            "game_short" => $gameShort,
        ]);
        return true;
    }

    /**
     * Trigger if a game has been uninstalled
     * @param string $gameShort
     * @return bool
     * @throws NitrapiException
     */
    public function uninstalled(string $gameShort): bool
    {
        $url = "/services/" . $this->getService()->getId() . "/gameservers/callback/uninstalled";
        $this->getService()->getApi()->dataPost($url, [
            "game_short" => $gameShort,
        ]);
        return true;
    }

    /**
     * Trigger if the service has been restarted
     * @return bool
     * @throws NitrapiException
     */
    public function restarted(): bool
    {
        $url = "/services/" . $this->getService()->getId() . "/gameservers/callback/restarted";
        $this->getService()->getApi()->dataPost($url);
        return true;
    }

    /**
     * Trigger if the complete service has been deleted
     * @return bool
     * @throws NitrapiException
     */
    public function deleted(): bool
    {
        $url = "/services/" . $this->getService()->getId() . "/gameservers/callback/deleted";
        $this->getService()->getApi()->dataPost($url);
        return true;
    }
}
