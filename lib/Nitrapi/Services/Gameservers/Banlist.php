<?php

namespace Nitrapi\Services\Gameservers;

class Banlist
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    /**
     * Get the ban list of the game server.
     *
     * @return array
     */
    public function getBanlist() {
        $url = "/services/".$this->service->getId()."/gameservers/games/banlist";
        $whitelist = $this->service->getApi()->dataGet($url);

        return $whitelist;
    }

    /**
     * Adds a player to the ban list.
     *
     * @param $identifier
     * @return string
     */
    public function addBanlist($identifier) {
        $url = "/services/".$this->service->getId()."/gameservers/games/banlist";
        return $this->service->getApi()->dataPost($url, [
            'identifier' => $identifier
        ]);
    }

    /**
     * Removes a player from the ban list.
     *
     * @param $identifier
     * @return string
     */
    public function removeBanlist($identifier) {
        $url = "/services/".$this->service->getId()."/gameservers/games/banlist";
        return $this->service->getApi()->dataDelete($url, [
            'identifier' => $identifier
        ]);
    }
}