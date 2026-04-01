<?php

namespace Nitrapi\Admin\Servers;

use Nitrapi\Admin\Admin;
use Nitrapi\Admin\Servers\IPMI\Ipmi;

class ServerManager
{
    protected $admin;

    public function __construct(Admin $admin) {
        $this->setAdmin($admin);
    }

    public function getIpmi(): Ipmi {
        return new Ipmi($this);
    }

    /**
     * @param Admin $admin
     */
    protected function setAdmin(Admin $admin): void {
        $this->admin = $admin;
    }

    /**
     * @return Admin
     */
    public function getAdmin(): Admin {
        return $this->admin;
    }
}
