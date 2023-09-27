<?php

namespace Nitrapi\Services\Voiceservers\Types;

use Nitrapi\Services\Voiceservers\Voiceserver;

abstract class Type
{
    /**
     * @var Voiceserver
     */
    protected $service;

    public function __construct(Voiceserver $service) {
        $this->service = $service;
    }
}
