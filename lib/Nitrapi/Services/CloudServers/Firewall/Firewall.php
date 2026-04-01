<?php

namespace Nitrapi\Services\CloudServers\Firewall;

use Nitrapi\Common\Exceptions\NitrapiException;
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
    protected $enabled;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @throws NitrapiException
     */
    public function __construct(CloudServer $service)
    {
        $this->service = $service;
        $this->refresh();
    }

    /**
     * Refresh firewall data
     *
     * @throws NitrapiException
     */
    public function refresh(): void
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/firewall";
        $firewall = $this->service->getApi()->dataGet($url)['firewall'];

        $this->enabled = $firewall['enabled'];
        $this->rules = $firewall['rules'];
    }

    /**
     * Returns the Firewall Status
     *
     * @return bool|null
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * Return the firewall rules
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Deletes a specific rule by number.
     *
     * @param $number
     * @return bool
     * @throws NitrapiException
     */
    public function deleteRule($number): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/firewall/remove";
        $this->service->getApi()->dataDelete($url, [
            'number' => $number,
        ]);
        $this->refresh();
        return true;
    }

    /**
     * Enables the Firewall.
     *
     * @return bool
     * @throws NitrapiException
     */
    public function enableFirewall(): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/firewall/enable";
        $this->service->getApi()->dataPost($url);
        $this->refresh();
        return true;
    }

    /**
     * Disables the Firewall.
     *
     * @return bool
     * @throws NitrapiException
     */
    public function disableFirewall(): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/firewall/disable";
        $this->service->getApi()->dataPost($url);
        $this->refresh();
        return true;
    }

    /**
     * Creates a new Firewall Rule.
     *
     * @param string|null $sourceIp
     * @param string|null $targetIp
     * @param int|null $targetPort
     * @param string $protocol
     * @param string $comment
     * @return bool
     * @throws NitrapiException
     */
    public function addRule(
        ?string $sourceIp = null,
        ?string $targetIp = null,
        ?int $targetPort = null,
        string $protocol = 'tcp',
        string $comment = 'Firewall rule'
    ): bool {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/firewall/add";
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
