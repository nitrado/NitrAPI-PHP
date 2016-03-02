<?php

namespace Nitrapi\Services\Voiceservers;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Voiceserver extends Service
{
    protected $info = null;

    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
        $this->info = $this->getApi()->dataGet("services/" . $this->getId() . "/voiceservers");
    }

    /**
     * Returns informations about the gameserver
     *
     * @return mixed
     */
    public function getDetails() {
        return new GameserverDetails($this->info['voiceserver']);
    }
}