<?php

namespace Nitrapi\Admin;

use Nitrapi\Nitrapi;

class Admin
{
    protected $api;

    public function __construct(Nitrapi &$api) {
        $this->setApi($api);
    }

    /**
     * Returns the ServerManager
     *
     * @return ServerManager
     */
    public function getServerManager() {
        return new ServerManager($this);
    }

    /**
     * @param Nitrapi $api
     */
    protected function setApi(Nitrapi $api) {
        $this->api = $api;
    }

    /**
     * @return Nitrapi
     */
    public function getApi() {
        return $this->api;
    }
}