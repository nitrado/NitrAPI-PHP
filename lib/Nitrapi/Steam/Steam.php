<?php

namespace Nitrapi\Steam;

use Nitrapi\Nitrapi;
use Nitrapi\Steam\Workshop\Workshop;

class Steam
{
    protected $api;

    public function __construct(Nitrapi &$api) {
        $this->setApi($api);
    }
    
    /**
     * Returns the Workshop Object
     *
     * @return ServerManager
     */
    public function getWorkshop() {
        return new Workshop($this);
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
