<?php

namespace Nitrapi\Services\Gameservers;

class CallbackHandler
{
    protected $api;

    public function __construct(\Nitrapi\Nitrapi $api) {
        $this->api = $api;
    }

    /**
     * Trigger if a game has been installed
     * @param string $gameShort
     * @return bool
     */
    public function installed($gameShort) {
    }

    /**
     * Trigger if a game has been uninstalled
     * @param string $gameShort
     * @return bool
     */
    public function uninstalled($gameShort) {
    }

    /**
     * Trigger if the service has been restarted
     * @return bool
     */
    public function restarted() {
    }

    /**
     * Trigger if the complete service has been deleted
     * @return bool
     */
    public function deleted() {
    }
}