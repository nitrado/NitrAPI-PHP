<?php

namespace Nitrapi\Services\Bouncers;


class Ident {
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    public function getPassword() {
        return $this->data['password'];
    }

    public function getIdent() {
        return $this->data['ident'];
    }

    public function getServer() {
        return $this->data['server']['name'];
    }

    public function getPort() {
        return $this->data['server']['port'];
    }

    public function hasRunningTasks() {
        return $this->data['running_tasks'];
    }
}
