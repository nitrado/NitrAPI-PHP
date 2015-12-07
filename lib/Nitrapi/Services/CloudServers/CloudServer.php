<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;
use Nitrapi\Common\Exceptions\NitrapiServiceTypeNotFoundException;

class CloudServer extends Services
{
    public function __construct(Nitrapi &$api, &$data) {
        parent::__construct($api, $data);
    }
}