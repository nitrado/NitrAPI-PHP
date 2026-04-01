<?php

namespace Nitrapi\Common;

use Nitrapi\Nitrapi;

abstract class NitrapiObject
{
    private $api;

    public function __construct(Nitrapi $api)
    {
        $this->setApi($api);
    }

    public function setApi(Nitrapi $api): void
    {
        $this->api = $api;
    }

    public function getApi(): Nitrapi
    {
        return $this->api;
    }
}
