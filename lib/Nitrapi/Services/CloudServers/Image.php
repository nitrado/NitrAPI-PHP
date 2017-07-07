<?php

namespace Nitrapi\Services\CloudServers;

class Image
{
    protected $id = null;
    protected $name = null;
    protected $isWindows = false;
    protected $default = false;
    protected $hasDaemon = false;
    protected $isDaemonCompatible = false;

    public function __construct($id, $name, $isWindows, $default, $hasDaemon, $isDaemonCompatible) {
        $this->id = $id;
        $this->name = $name;
        $this->isWindows = $isWindows;
        $this->default = $default;
        $this->hasDaemon = $hasDaemon;
        $this->isDaemonCompatible = $isDaemonCompatible;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function isWindows() {
        return $this->isWindows;
    }

    public function isDefault() {
        return $this->default;
    }

    public function hasDaemon() {
        return $this->hasDaemon;
    }

    public function isDaemonCompatible() {
        return $this->isDaemonCompatible;
    }
}