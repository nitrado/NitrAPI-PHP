<?php

namespace Nitrapi\Services\CloudServers;


class CloudServerDetails
{
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    /**
     * Returns the current Cloud Server status
     *
     * @return string
     */
    public function getStatus() {
        return (string)$this->data['status'];
    }

    /**
     * Returns the Cloud Server hostname
     *
     * @return string
     */
    public function getHostname() {
        return (string)$this->data['hostname'];
    }

    /**
     * Returns the Cloud Server ip
     *
     * @return string
     */
    public function getIP() {
        return (string)$this->data['ip'];
    }

    /**
     * Returns the initial admin password
     *
     * @return string
     */
    public function getInitialPassword() {
        return (string)$this->data['initial_password'];
    }

}