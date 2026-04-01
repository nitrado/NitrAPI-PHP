<?php

namespace Nitrapi\Steam;

use Nitrapi\Nitrapi;
use Nitrapi\Steam\Workshop\Workshop;

class Steam
{
    protected $api;

    public function __construct(Nitrapi $api)
    {
        $this->setApi($api);
    }

    /**
     * Returns the Workshop Object
     */
    public function getWorkshop(): Workshop
    {
        return new Workshop($this);
    }

    protected function setApi(Nitrapi $api): void
    {
        $this->api = $api;
    }

    public function getApi(): Nitrapi
    {
        return $this->api;
    }
}
