<?php

namespace Nitrapi\Services\Webspaces;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Webspace extends Service
{
    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
    }
}