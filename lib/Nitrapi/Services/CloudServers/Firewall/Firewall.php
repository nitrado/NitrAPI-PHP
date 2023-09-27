<?php

namespace Nitrapi\Services\CloudServers\Firewall;

use Nitrapi\Services\CloudServers\CloudServer;

class Firewall
{
    /**
     * @var CloudServer $service
     */
    protected $service;

    /**
     * @var bool
     */
    protected $enabled = null;

    /**
     * @var array
     */
    protected $rules = [];

    public function __construct(CloudServer $service) {
        $this->service = $service;
        $this->refresh();
    }

    /**
     * Refresh firewall data
     */
    public function refresh() {
        $url = "/services/".$this->service->getId()."/cloud_servers/firewall";
        $firewall = $this->service->getApi()->dataGet($url)['firewall'];

        $this->enabled = $firewall['enabled'];
        $this->rules = $firewall['rules'];
    }

    /**
     * Returns the Firewall Status
     *
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * Return the firewall rules
     *
     * @return array
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * Deletes a specific rule by number.
     *
     * @param $number
     * @return bool
     */
    public function deleteRule($number) {
        $url = "/services/".$this->service->getId()."/cloud_servers/firewall/remove";
        $this->service->getApi()->dataDelete($url, [
            'number' => $number
        ]);
        $this->refresh();
        return true;
    }

    /**
     * Enables the Firewall.
     *
     * @return bool
     */
    public function enableFirewall() {
        $url = "/services/".$this->service->getId()."/cloud_servers/firewall/enable";
        $this->service->getApi()->dataPost($url);
        $this->refresh();
        return true;
    }

    /**
     * Disables the Firewall.
     *
     * @return bool
     */
    public function disableFirewall() {
        $url = "/services/".$this->service->getId()."/cloud_servers/firewall/disable";
        $this->service->getApi()->dataPost($url);
        $this->refresh();
        return true;
    }

    /**
     * Creates a new Firewall Rule.
     *
     * @param $sourceIp string
     * @param $targetIp string
     * @param $targetPort integer
     * @param $protocol string
     * @param $comment string
     * @return bool
     */
    public function addRule($sourceIp = null, $targetIp = null, $targetPort = null, $protocol = 'tcp', $comment = 'Firewall rule') {
        $url = "/services/".$this->service->getId()."/cloud_servers/firewall/add";
        $this->service->getApi()->dataPost($url, [
            'source_ip' => $sourceIp,
            'target_ip' => $targetIp,
            'target_port' => $targetPort,
            'protocol' => $protocol,
            'comment' => $comment,
        ]);
        $this->refresh();
        return true;
    }

}
