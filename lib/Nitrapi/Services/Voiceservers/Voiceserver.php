<?php

namespace Nitrapi\Services\Voiceservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Voiceserver extends Service
{
    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
    }
}