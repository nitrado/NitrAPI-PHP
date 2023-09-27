<?php

namespace Nitrapi\Services\CloudServers\Backup;

use Nitrapi\Services\CloudServers\CloudServer;

class BackupManager
{
    /**
     * @var CloudServer $service
     */
    protected $service;

    public function __construct(CloudServer $service) {
        $this->service = $service;
    }

    /**
     * @return CloudServer
     */
    public function getCloudServer() {
        return $this->service;
    }

    /**
     * Return all Cloud Server Backups
     */
    public function getBackups() {
        $url = "/services/".$this->service->getId()."/cloud_servers/backups";
        $backups = [];

        foreach ($this->service->getApi()->dataGet($url)['backups'] as $backup) {
            $backups[] = new Backup($this, $backup);
        }

        return $backups;
    }

    /**
     * Create a new Cloud Server Backup.
     * This action can take some minutes.
     *
     * @return bool
     */
    public function createBackup() {
        $url = "/services/".$this->service->getId()."/cloud_servers/backups";
        $this->service->getApi()->dataPost($url);

        return true;
    }

}
