<?php

namespace Nitrapi\Services\Gameservers;

class Adminlist
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver $service) {
        $this->service = $service;
    }

    /**
     * Returns the admin list from the game server.
     *
     * @return array
     */
    public function getAdminlist() {
        $url = "/services/".$this->service->getId()."/gameservers/games/adminlist";
        $whitelist = $this->service->getApi()->dataGet($url);

        return $whitelist;
    }

    /**
     * Adds a player as admin to the game server.
     *
     * @param $identifier
     * @return string
     */
    public function addAdminlist($identifier) {
        $url = "/services/".$this->service->getId()."/gameservers/games/adminlist";
        return $this->service->getApi()->dataPost($url, [
            'identifier' => $identifier
        ]);
    }

    /**
     * Removes a admin player from the game server.
     *
     * @param $identifier
     * @return string
     */
    public function removeAdminlist($identifier) {
        $url = "/services/".$this->service->getId()."/gameservers/games/adminlist";
        return $this->service->getApi()->dataDelete($url, [
            'identifier' => $identifier
        ]);
    }
}
