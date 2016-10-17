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
     * Returns the main ip address of the server
     *
     * @return string
     */
    public function getMainIP() {
        foreach ($this->getIPs() as $ip) {
            if ($ip['main_ip'] && $ip['version'] == 4) {
                return $ip['address'];
            }
        }

        return null;
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

    /**
     * Return true if the bandwdith is currently limited
     *
     * @return boolean
     */
    public function isBandwidthLimited() {
        return (bool)$this->data['bandwidth_limited'];
    }

    /**
     * Returns the ID of the currently installed image.
     *
     * @return int
     */
    public function getImageId() {
        return $this->data['image']['id'];
    }

    /**
     * Returns the name of the currently installed image, as displayed to the user.
     *
     * @return string
     */
    public function getImageName() {
        return $this->data['image']['name'];
    }

}