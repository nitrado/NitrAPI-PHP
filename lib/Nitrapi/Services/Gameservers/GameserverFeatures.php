<?php

namespace Nitrapi\Services\Gameservers;


class GameserverFeatures
{
    protected $data;

    public function __construct(array &$data) {
        $this->data = $data;
    }

    public function hasBackups() {
        return $this->data['has_backups'];
    }

    public function hasPackages() {
        return $this->data['has_packages'];
    }

    public function hasApplicationServer() {
        return $this->data['has_application_server'];
    }

    public function hasFileBrowser() {
        return $this->data['has_file_browser'];
    }

    public function hasExpertMode() {
        return $this->data['has_expert_mode'];
    }

    public function hasDatabase() {
        return $this->data['has_database'];
    }

    public function hasRestartMessageSupport() {
        return $this->data['has_restart_message_support'];
    }

    public function hasFTP() {
        return $this->data['has_ftp'];
    }
}