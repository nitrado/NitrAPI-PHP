<?php

namespace Nitrapi\Services\CloudServers;

class Image
{
    protected $id = null;
    protected $name = null;
    protected $isWindows = false;

    public function __construct($id, $name, $isWindows) {
        $this->id = $id;
        $this->name = $name;
        $this->isWindows = $isWindows;
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
}