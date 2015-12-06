<?php

namespace Nitrapi\Services\CloudServers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class CloudServer extends Service
{
    protected $game;
    protected $info = null;

    public function __construct(Nitrapi &$api, &$data) {
        parent::__construct($api, $data);
        $this->info = $this->getApi()->dataGet("services/" . $this->getId() . "/cloud_servers");
    }

    public function refresh() {
        $url = "services/" . $this->getId() . "/cloud_servers";
        $this->info = $this->getApi()->dataGet($url);
    }

    /**
     * Returns informations about the gameserver
     *
     * @return mixed
     */
    public function getDetails() {
        return new CloudServerDetails($this->info['cloud_server']);
    }
}