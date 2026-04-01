<?php

namespace Nitrapi\Services\CloudServers\Backup;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\CloudServers\CloudServer;

class BackupManager
{
    /**
     * @var CloudServer $service
     */
    protected $service;

    public function __construct(CloudServer $service)
    {
        $this->service = $service;
    }

    /**
     * @return CloudServer
     */
    public function getCloudServer(): CloudServer
    {
        return $this->service;
    }

    /**
     * Return all Cloud Server Backups
     * @throws NitrapiException
     */
    public function getBackups(): array
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/backups";
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
     * @throws NitrapiException
     */
    public function createBackup(): bool
    {
        $url = "/services/" . $this->service->getId() . "/cloud_servers/backups";
        $this->service->getApi()->dataPost($url);

        return true;
    }
}
