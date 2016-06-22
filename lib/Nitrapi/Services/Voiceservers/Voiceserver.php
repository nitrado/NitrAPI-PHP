<?php

namespace Nitrapi\Services\Voiceservers;

use Nitrapi\Common\Exceptions\NitrapiServiceTypeNotFoundException;
use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Voiceserver extends Service
{
    protected $info = null;

    public function __construct(Nitrapi $api, $id) {
        parent::__construct($api, $id);
        $this->info = $this->getApi()->dataGet("services/" . $this->getId() . "/voiceservers");
    }

    public function refresh() {
        $url = "services/" . $this->getId() . "/voiceservers";
        $this->info = $this->getApi()->dataGet($url);
    }

    /**
     * Returns informations about the voiceserver
     *
     * @return VoiceserverDetails
     */
    public function getDetails() {
        return new VoiceserverDetails($this->info['voiceserver']);
    }

    /**
     * Restarts the voiceserver
     *
     * @return bool
     */
    public function doRestart() {
        $url = "services/" . $this->getId() . "/voiceservers/restart";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Stopps the voiceserver
     *
     * @return bool
     */
    public function doStop() {
        $url = "services/" . $this->getId() . "/voiceservers/stop";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Stopps the voiceserver
     *
     * @return bool
     */
    public function doReinstall() {
        $url = "services/" . $this->getId() . "/voiceservers/reinstall";
        $this->getApi()->dataPost($url);

        return true;
    }

    /**
     * Configures the voiceserver
     *
     * @return bool
     */
    public function doConfigChange($key, $value) {
        $url = "services/" . $this->getId() . "/voiceservers";
        $this->getApi()->dataPost($url, [
            'key' => $key,
            'value' => $value
        ]);

        return true;
    }

    /**
     * Returns all available Backups
     *
     * @return array
     */
    public function getBackups() {
        if (!isset($this->info['voiceserver']['specific']['snapshots'])) {
            return [];
        }

        return $this->info['voiceserver']['specific']['snapshots'];
    }

    /**
     * Creates a new Backup
     *
     * @return array
     */
    public function createBackup() {
        $url = "services/" . $this->getId() . "/voiceservers/backup";
        $result = $this->getApi()->dataPost($url)['snapshot'];

        $this->refresh();
        return $result;
    }

    /**
     * Deployes a specific snapshot to the Voiceserver
     * The server will be restarted after a successful deployment.
     *
     * @param $id
     * @return bool
     */
    public function restoreBackup($id) {
        $url = "services/" . $this->getId() . "/voiceservers/backup/".(int)$id."/restore";
        $this->getApi()->dataPost($url);

        return true;
    }

    /**
     * Deletes a specific Backup from the Voiceserver
     *
     * @param $id
     * @return bool
     */
    public function deleteBackup($id) {
        $url = "services/" . $this->getId() . "/voiceservers/backup/".(int)$id;
        $this->getApi()->dataDelete($url);

        $this->refresh();
        return true;
    }

    /**
     * Downloads a specific backup file
     *
     * @param $id
     * @return string
     */
    public function downloadBackup($id) {
        $url = "services/" . $this->getId() . "/voiceservers/backup/".(int)$id;
        $backup = $this->getApi()->dataGet($url)['snapshot'];

        return base64_decode($backup);
    }

    public function uploadBackup($backup) {
        $url = "services/" . $this->getId() . "/voiceservers/backup/upload/";
        $result = $this->getApi()->dataPost($url, null, null, [
            'body' => base64_encode($backup)
        ])['snapshot'];

        $this->refresh();
        return $result;
    }

    /**
     * Returns a voiceserver type instance
     **/
    public function getVoiceserverTypeInstance() {
        $class = "Nitrapi\\Services\\Voiceservers\\Types\\" . ucfirst($this->getDetails()->getType());

        if (!class_exists($class)) {
            throw new NitrapiServiceTypeNotFoundException("Voiceserver Type " . $this->getDetails()->getType() . " not found");
        }

        return new $class($this);
    }
}