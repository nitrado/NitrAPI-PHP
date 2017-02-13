<?php

namespace Nitrapi\Services\CloudServers\Backup;

class Backup
{
    /**
     * @var BackupManager $backupManager
     */
    protected $backupManager;

    protected $data;

    public function __construct(BackupManager &$backupManager, array $data) {
        $this->backupManager = $backupManager;
        $this->data = $data;
    }

    public function getId() {
        return $this->data['id'];
    }

    public function getName() {
        return $this->data['name'];
    }

    public function getStatus() {
        return $this->data['status'];
    }

    public function getCreatedAt() {
        return (new \DateTime())->setTimestamp(strtotime($this->data['created_at']));
    }

    public function getType() {
        return $this->data['type'];
    }

    /**
     * Restores the specific Backup to the Cloud Server.
     * This will take up to 30 minutes.
     *
     * The Cloud Server instance will reset to the Backup, this causes data-loss.
     *
     * @return bool
     */
    public function doRestore() {
        $url = "/services/".$this->backupManager->getCloudServer()->getId()."/cloud_servers/backups/" . $this->getId() . "/restore";
        $this->backupManager->getCloudServer()->getApi()->dataPost($url);

        return true;
    }

    /**
     * Deletes a specific Backup.
     *
     * Information:
     * Automatic Backups can't be deleted and will throw a Exception.
     *
     * @return bool
     */
    public function doDelete() {
        $url = "/services/".$this->backupManager->getCloudServer()->getId()."/cloud_servers/backups/" . $this->getId();
        $this->backupManager->getCloudServer()->getApi()->dataDelete($url);

        return true;
    }
}