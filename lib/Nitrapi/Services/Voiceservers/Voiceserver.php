<?php

namespace Nitrapi\Services\Voiceservers;

use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiServiceTypeNotFoundException;
use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class Voiceserver extends Service
{
    protected $info;

    /**
     * @throws NitrapiException
     */
    public function __construct(Nitrapi $api, $data)
    {
        parent::__construct($api, $data);

        if ($this->isActive()) {
            $this->info = $this->getApi()->dataGet("services/" . $this->getId() . "/voiceservers");
        }
    }

    /**
     * @throws NitrapiException
     */
    public function refresh(): void
    {
        $url = "services/" . $this->getId() . "/voiceservers";
        $this->info = $this->getApi()->dataGet($url);
    }

    /**
     * Returns information about the voiceserver
     *
     * @return VoiceserverDetails
     * @throws NitrapiErrorException
     */
    public function getDetails(): VoiceserverDetails
    {
        if (!isset($this->info['voiceserver'])) {
            throw new NitrapiErrorException('No voiceserver data available');
        }
        return new VoiceserverDetails($this->info['voiceserver']);
    }

    /**
     * Restarts the voiceserver
     *
     * @return bool
     * @throws NitrapiException
     */
    public function doRestart(): bool
    {
        $url = "services/" . $this->getId() . "/voiceservers/restart";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Stops the voiceserver
     *
     * @return bool
     * @throws NitrapiException
     */
    public function doStop(): bool
    {
        $url = "services/" . $this->getId() . "/voiceservers/stop";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Stops the voiceserver
     *
     * @return bool
     * @throws NitrapiException
     */
    public function doReinstall(): bool
    {
        $url = "services/" . $this->getId() . "/voiceservers/reinstall";
        $this->getApi()->dataPost($url);

        return true;
    }

    /**
     * Configures the voiceserver
     *
     * @throws NitrapiException
     */
    public function doConfigChange($key, $value): bool
    {
        $url = "services/" . $this->getId() . "/voiceservers";
        $this->getApi()->dataPost($url, [
            'key' => $key,
            'value' => $value,
        ]);

        return true;
    }

    /**
     * Returns all available Backups
     */
    public function getBackups(): array
    {
        return $this->info['voiceserver']['specific']['snapshots'] ?? [];
    }

    /**
     * Creates a new Backup
     *
     * @throws NitrapiException
     */
    public function createBackup(): array
    {
        $url = "services/" . $this->getId() . "/voiceservers/backup";
        $result = $this->getApi()->dataPost($url)['snapshot'];

        $this->refresh();
        return $result;
    }

    /**
     * Deployes a specific snapshot to the Voiceserver
     * The server will be restarted after a successful deployment.
     *
     * @throws NitrapiException
     */
    public function restoreBackup($id): bool
    {
        $url = "services/" . $this->getId() . "/voiceservers/backup/" . (int)$id . "/restore";
        $this->getApi()->dataPost($url);

        return true;
    }

    /**
     * Deletes a specific Backup from the Voiceserver
     *
     * @throws NitrapiException
     */
    public function deleteBackup($id): bool
    {
        $url = "services/" . $this->getId() . "/voiceservers/backup/" . (int)$id;
        $this->getApi()->dataDelete($url);

        $this->refresh();
        return true;
    }

    /**
     * Downloads a specific backup file
     *
     * @throws NitrapiException
     */
    public function downloadBackup($id): string
    {
        $url = "services/" . $this->getId() . "/voiceservers/backup/" . (int)$id;
        $backup = $this->getApi()->dataGet($url)['snapshot'];

        return base64_decode($backup);
    }

    /**
     * @throws NitrapiException
     */
    public function uploadBackup($backup)
    {
        $url = "services/" . $this->getId() . "/voiceservers/backup/upload/";
        $result = $this->getApi()->dataPost($url, null, null, [
            'body' => base64_encode($backup),
        ])['snapshot'];

        $this->refresh();
        return $result;
    }

    /**
     * Returns a voiceserver type instance
     * @throws NitrapiServiceTypeNotFoundException
     * @throws NitrapiException
     */
    public function getVoiceserverTypeInstance()
    {
        $class = "Nitrapi\\Services\\Voiceservers\\Types\\" . ucfirst($this->getDetails()->getType());

        if (!class_exists($class)) {
            throw new NitrapiServiceTypeNotFoundException(
                "Voiceserver Type " . $this->getDetails()->getType() . " not found",
            );
        }

        return new $class($this);
    }
}
