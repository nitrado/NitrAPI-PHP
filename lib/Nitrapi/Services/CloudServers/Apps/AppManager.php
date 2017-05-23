<?php

namespace Nitrapi\Services\CloudServers\Apps;

use Nitrapi\Services\CloudServers\CloudServer;

class AppManager {
    /**
     * @var CloudServer $service
     */
    public $service;

    public function __construct(CloudServer $service) {
        $this->service = $service;
    }

    public function getInstalledApps() {
        $url = '/services/' . $this->service->getId() . '/cloud_servers/apps';
        $apps = [];
        foreach ($this->service->getApi()->dataGet($url)['apps'] as $app)
            $apps[] = new App($this, $app);
        return $apps;
    }

    public function getAvailableAppDescriptions() {
        $url = '/services/' . $this->service->getId() . '/cloud_servers/apps/available';
        $apps = [];
        foreach ($this->service->getApi()->dataGet($url)['apps'] as $description)
            $apps[] = new AppDescription($description);
        return $apps;
    }
}
