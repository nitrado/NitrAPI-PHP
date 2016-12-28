<?php

namespace Nitrapi\Services\Gameservers\Packages;

use Nitrapi\Common\Exceptions\NitrapiErrorException;

class Package {
    /**
     * Package constructor.
     * @param Service $serviced
     * @param Package $name
     * @param Description $description
     * @param Status $status
     */
    public function __construct($service, $name, $description, $status) {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->service = $service;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function install() {
        // TODO: Update status
        return $this->perform_action('install');
    }
    public function uninstall() {
        // TODO: Update status
        return $this->perform_action('uninstall');
    }
    public function reinstall() {
        // TODO: Update status
        return $this->perform_action('reinstall');
    }

    private function perform_action($action) {
        $actions = array('install', 'uninstall', 'reinstall');
        if (!in_array($action, $actions))
            throw new NitrapiErrorException("The action #{action} can't be executed.");

        $url = "/services/".$this->service->getId()."/gameservers/packages/".$action;
        return $this->service->getApi()->dataPost($url, array(
            "package" => $this->name
        ));
    }
}
