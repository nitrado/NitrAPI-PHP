<?php

namespace Nitrapi\Services\Gameservers;

class CallbackHandler
{
    protected $service;

    public function __construct(Gameserver $service) {
        $this->service = $service;
    }

    public function getService() {
        return $this->service;
    }

    /**
     * Trigger if a game has been installed
     * @param string $gameShort
     * @return bool
     */
    public function installed($gameShort) {
        $url = "/services/".$this->getService()->getId()."/gameservers/callbacks/installed";
        $this->getService()->getApi()->dataPost($url, array(
            "game_short" => $gameShort
        ));
        return true;
    }

    /**
     * Trigger if a game has been uninstalled
     * @param string $gameShort
     * @return bool
     */
    public function uninstalled($gameShort) {
        $url = "/services/".$this->getService()->getId()."/gameservers/callbacks/uninstalled";
        $this->getService()->getApi()->dataPost($url, array(
            "game_short" => $gameShort
        ));
        return true;
    }

    /**
     * Trigger if the service has been restarted
     * @return bool
     */
    public function restarted() {
        $url = "/services/".$this->getService()->getId()."/gameservers/callbacks/restarted";
        $this->getService()->getApi()->dataPost($url);
        return true;
    }

    /**
     * Trigger if the complete service has been deleted
     * @return bool
     */
    public function deleted() {
        $url = "/services/".$this->getService()->getId()."/gameservers/callbacks/deleted";
        $this->getService()->getApi()->dataPost($url);
        return true;
    }
}