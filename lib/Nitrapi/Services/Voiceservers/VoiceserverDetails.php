<?php

namespace Nitrapi\Services\Voiceservers;


class VoiceserverDetails
{
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    public function getType() {
        return (string)$this->data['type'];
    }

    public function getIP() {
        return (string)$this->data['ip'];
    }

    public function isStarted() {
        return (bool)$this->data['started'];
    }

    public function isStopped() {
        return !$this->isStarted();
    }

    public function getPort() {
        return (int)$this->data['port'];
    }

    public function getSlots() {
        return (int)$this->data['slots'];
    }
}