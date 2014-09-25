<?php

namespace Nitrapi\Services\Rootservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Rootserver extends Service
{
    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
    }
}