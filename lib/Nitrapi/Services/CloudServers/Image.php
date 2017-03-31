<?php

namespace Nitrapi\Services\CloudServers;

class Image
{
    protected $id = null;
    protected $name = null;
    protected $isWindows = false;
    protected $default = false;

    public function __construct($id, $name, $isWindows, $default) {
        $this->id = $id;
        $this->name = $name;
        $this->isWindows = $isWindows;
        $this->default = $default;
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
}