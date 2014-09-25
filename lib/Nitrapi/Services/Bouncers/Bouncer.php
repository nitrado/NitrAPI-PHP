<?php

namespace Nitrapi\Services\Bouncers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Bouncer extends Service
{
    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
    }
}