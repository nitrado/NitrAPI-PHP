<?php

namespace Nitrapi\Services\Gameservers;

class Whitelist
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    /**
     * Returns the whitelist of the game server.
     *
     * @return array
     */
    public function getWhitelist() {
        $url = "/services/".$this->service->getId()."/gameservers/games/whitelist";
        $whitelist = $this->service->getApi()->dataGet($url);

        return $whitelist;
    }

    /**
     * Adds a player to the whitelist.
     *
     * @param $identifier
     * @return string
     */
    public function addWhitelist($identifier) {
        $url = "/services/".$this->service->getId()."/gameservers/games/whitelist";
        return $this->service->getApi()->dataPost($url, [
            'identifier' => $identifier
        ]);
    }

    /**
     * Removes a player from the whitelist.
     *
     * @param $identifier
     * @return string
     */
    public function removeWhitelist($identifier)
    {
        $url = "/services/" . $this->service->getId() . "/gameservers/games/whitelist";
        return $this->service->getApi()->dataDelete($url, [
            'identifier' => $identifier
        ]);
    }
}