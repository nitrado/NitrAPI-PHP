<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Nitrapi;

abstract class Pricing {

    /**
     * @var Nitrapi
     */
    protected $nitrapi;

    public function __construct(Nitrapi $nitrapi)
    {
        $this->nitrapi = $nitrapi;
    }
}