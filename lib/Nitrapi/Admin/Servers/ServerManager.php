<?php

namespace Nitrapi\Admin\Servers;

use Nitrapi\Admin\Servers\IPMI\Ipmi;

class ServerManager
{
    protected $admin;

    public function __construct(Nitrapi\Admin\Admin $admin) {
        $this->setAdmin($admin);
    }

    public function getIpmi() {
        return new Ipmi($this);
    }

    /**
     * @param Admin $admin
     */
    protected function setAdmin(Admin $admin) {
        $this->admin = $admin;
    }

    /**
     * @return Admin
     */
    public function getAdmin() {
        return $this->admin;
    }
}