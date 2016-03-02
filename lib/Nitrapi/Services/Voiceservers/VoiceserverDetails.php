<?php

namespace Nitrapi\Services\Voiceservers;


class VoiceserverDetails
{
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    public function getIP() {
        return (string)$this->data['ip'];
    }

    public function getPort() {
        return (int)$this->data['port'];
    }

    public function getSlots() {
        return (int)$this->data['slots'];
    }
}