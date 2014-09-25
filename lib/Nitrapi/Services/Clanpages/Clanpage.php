<?php

namespace Nitrapi\Services\Clanpages;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Clanpage extends Service
{
    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
    }
}