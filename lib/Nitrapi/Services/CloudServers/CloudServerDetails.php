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
     * Returns the Cloud Server ips
     *
     * @return array
     */
    public function getIPs() {
        return (array)$this->data['ips'];
    }

    /**
     * Returns the Hardware information
     *
     * @return array
     */
    public function getHardwareInfo() {
        return (array)$this->data['hardware'];
    }

    /**
     * Return true if the initial password is available
     *
     * @return boolean
     */
    public function isPasswordAvailable() {
        return (bool)$this->data['password_available'];
    }

}