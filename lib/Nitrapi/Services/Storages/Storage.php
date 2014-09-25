<?php

namespace Nitrapi\Services\Storages;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Storage extends Service
{
    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
    }
}