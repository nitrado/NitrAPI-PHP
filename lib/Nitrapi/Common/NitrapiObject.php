<?php

namespace Nitrapi\Common;

use Nitrapi\Nitrapi;

abstract class NitrapiObject
{
    private $api;

    public function __construct(Nitrapi &$api)
    {
        $this->setApi($api);
    }

    /**
     * @param Nitrapi $api
     */
    public function setApi(Nitrapi &$api)
    {
        $this->api = $api;
    }

    /**
     * @return Nitrapi
     */
    public function getApi()
    {
        return $this->api;
    }
}
