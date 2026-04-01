<?php

namespace Nitrapi\Admin\Servers\IPMI;

use Nitrapi\Admin\Servers\ServerManager;
use Nitrapi\Common\Exceptions\NitrapiException;

class Ipmi
{
    protected $serverManager;

    public function __construct(ServerManager $serverManager) {
        $this->setServerManager($serverManager);
    }

    /**
     * @throws NitrapiException
     */
    public function getIKVM($hostname): bool
    {
        return $this->getServerManager()->getAdmin()->getApi()->dataGet('/admin/server/ipmi/ikvm', null, [
            'query' => [
                'hostname' => $hostname
            ]
        ])['ipmi'];
    }

    /**
     * @throws NitrapiException
     */
    public function doReset($hostname): bool
    {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/reset', [
            'hostname' => $hostname
        ]);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function doPowerOff($hostname): bool
    {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/power_off', [
            'hostname' => $hostname
        ]);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function doPowerOn($hostname): bool
    {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/power_on', [
            'hostname' => $hostname
        ]);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function doIKVMReset($hostname): bool
    {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/ikvm_reset', [
            'hostname' => $hostname
        ]);
        return true;
    }

    /**
     * @param ServerManager $serverManager
     */
    protected function setServerManager(ServerManager $serverManager): void
    {
        $this->serverManager = $serverManager;
    }

    /**
     * @return ServerManager
     */
    public function getServerManager(): ServerManager
    {
        return $this->serverManager;
    }
}
