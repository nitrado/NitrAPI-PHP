<?php

namespace Nitrapi\Admin\Ipmi;

use Nitrapi\Admin\ServerManager;

class Ipmi
{
    protected $serverManager;

    public function __construct(ServerManager $serverManager) {
        $this->setServerManager($serverManager);
    }

    /**
     * @return bool
     */
    public function getIKVM($hostname) {
        return $this->getServerManager()->getAdmin()->getApi()->dataGet('/admin/server/ipmi/ikvm', null, [
            'query' => [
                'hostname' => $hostname
            ]
        ])['ipmi'];
    }

    public function doReset($hostname) {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/reset', [
            'hostname' => $hostname
        ]);
        return true;
    }

    public function doPowerOff($hostname) {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/power_off', [
            'hostname' => $hostname
        ]);
        return true;
    }

    public function doPowerOn($hostname) {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/power_on', [
            'hostname' => $hostname
        ]);
        return true;
    }

    public function doIKVMReset($hostname) {
        $this->getServerManager()->getAdmin()->getApi()->dataPost('/admin/server/ipmi/ikvm_reset', [
            'hostname' => $hostname
        ]);
        return true;
    }

    /**
     * @param ServerManager $serverManager
     */
    protected function setServerManager(ServerManager $serverManager) {
        $this->serverManager = $serverManager;
    }

    /**
     * @return ServerManager
     */
    public function getServerManager() {
        return $this->serverManager;
    }
}