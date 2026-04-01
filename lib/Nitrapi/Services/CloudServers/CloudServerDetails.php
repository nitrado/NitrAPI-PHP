<?php

namespace Nitrapi\Services\CloudServers;


class CloudServerDetails
{
    protected $data;

    public function __construct(array &$data)
    {
        $this->data = $data;
    }

    /**
     * Returns the current Cloud Server status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->data['status'];
    }

    /**
     * Returns the Cloud Server hostname
     *
     * @return string
     */
    public function getHostname(): string
    {
        return (string)$this->data['hostname'];
    }

    /**
     * Returns the Dynamic Cloud Server
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        return (bool)$this->data['dynamic'];
    }

    /**
     * Returns the main ip address of the server
     *
     * @return string|null
     */
    public function getMainIP(): ?string
    {
        foreach ($this->getIPs() as $ip) {
            if ($ip['main_ip'] && $ip['version'] === 4) {
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
    public function getIPs(): array
    {
        return (array)$this->data['ips'];
    }

    /**
     * Returns the Hardware information
     *
     * @return array
     */
    public function getHardwareInfo(): array
    {
        return (array)$this->data['hardware'];
    }

    /**
     * Return true if the initial password is available
     *
     * @return boolean
     */
    public function isPasswordAvailable(): bool
    {
        return (bool)$this->data['password_available'];
    }

    /**
     * Return true if the bandwdith is currently limited
     *
     * @return boolean
     */
    public function isBandwidthLimited(): bool
    {
        return (bool)$this->data['bandwidth_limited'];
    }

    /**
     * Returns the ID of the currently installed image.
     *
     * @return int
     */
    public function getImageId(): int
    {
        return $this->data['image']['id'];
    }

    /**
     * Returns the name of the currently installed image, as displayed to the user.
     *
     * @return string
     */
    public function getImageName(): string
    {
        return $this->data['image']['name'];
    }

    /**
     * Returns true if the Cloud Server has a Nitrapi Daemon instance running.
     *
     * @return bool
     */
    public function hasDaemonSupport(): bool
    {
        return $this->data['daemon_available'];
    }
}
