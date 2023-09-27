<?php

namespace Nitrapi\Services\Gameservers\LicenseKeys;

use Nitrapi\Services\Gameservers\Gameserver;
use Nitrapi\Services\ServiceItem;

class LicenseKey extends ServiceItem
{
    /**
     * @var Gameserver $service
     */
    protected $service;

    protected $id;
    protected $assigned_at;
    protected $game_short;
    protected $key;
    protected $type;

    public function __construct(Gameserver $service, array &$data) {
        parent::__construct($service, $data);
        $this->setService($service);
    }

    public function getId() {
        return (int)$this->id;
    }

    public function getKey() {
        return $this->key;
    }

    public function getType() {
        return $this->type;
    }

    public function getGame() {
        return $this->game_short;
    }

    public function getAssignedDateTime() {
        return new \DateTime($this->assigned_at);
    }
}
