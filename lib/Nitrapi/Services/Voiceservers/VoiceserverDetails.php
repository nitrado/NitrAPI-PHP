<?php

namespace Nitrapi\Services\Voiceservers;

class VoiceserverDetails
{
    protected $data;

    public function __construct(array &$data)
    {
        $this->data = $data;
    }

    public function getType(): string
    {
        return (string)$this->data['type'];
    }

    public function getIP(): string
    {
        return (string)$this->data['ip'];
    }

    public function isStarted(): bool
    {
        return (bool)$this->data['started'];
    }

    public function isStopped(): bool
    {
        return !$this->isStarted();
    }

    public function getPort(): int
    {
        return (int)$this->data['port'];
    }

    public function getSlots(): int
    {
        return (int)$this->data['slots'];
    }

    public function getSettingKeys()
    {
        return $this->data['setting_keys'];
    }

    public function getSpecificInformation()
    {
        return $this->data['specific'];
    }
}
