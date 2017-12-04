<?php

namespace Nitrapi\Services\Gameservers;

class Player {

    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    /**
     * Return a list of all online players.
     *
     * @return array
     */
    public function getOnlinePlayers() {
        $url = "/services/".$this->service->getId()."/gameservers/games/players";
        $whitelist = $this->service->getApi()->dataGet($url);

        return $whitelist;
    }
}