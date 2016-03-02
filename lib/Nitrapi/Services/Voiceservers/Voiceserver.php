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

    public function refresh() {
        $url = "services/" . $this->getId() . "/voiceservers";
        $this->info = $this->getApi()->dataGet($url);
    }

    /**
     * Returns informations about the voiceserver
     *
     * @return VoiceserverDetails
     */
    public function getDetails() {
        return new VoiceserverDetails($this->info['voiceserver']);
    }

    /**
     * Restarts the voiceserver
     *
     * @return bool
     */
    public function doRestart() {
        $url = "services/" . $this->getId() . "/voiceservers/restart";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Stopps the voiceserver
     *
     * @return bool
     */
    public function doStop() {
        $url = "services/" . $this->getId() . "/voiceservers/stop";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Stopps the voiceserver
     *
     * @return bool
     */
    public function doReinstall() {
        $url = "services/" . $this->getId() . "/voiceservers/reinstall";
        $this->getApi()->dataPost($url);

        return true;
    }
}