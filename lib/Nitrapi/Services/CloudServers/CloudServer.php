<?php

namespace Nitrapi\Services\CloudServers;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

class CloudServer extends Service
{
    protected $game;
    protected $info = null;

    public function __construct(Nitrapi &$api, &$data) {
        parent::__construct($api, $data);
        $this->refresh();
    }

    public function refresh() {
        if ($this->getStatus() == self::SERVICE_STATUS_ACTIVE) {
            $url = "services/" . $this->getId() . "/cloud_servers";
            $this->info = $this->getApi()->dataGet($url);
        }
    }

    /**
     * Return information about the Cloud Server.
     *
     * @return CloudServerDetails
     */
    public function getDetails() {
        return new CloudServerDetails($this->info['cloud_server']);
    }

    /**
     * List all the users (with groups) on a Cloud Server. This users
     * are located in the /etc/passwd. All newly creates users on the
     * system are included in this array.
     *
     * @return array
     */
    public function getUsers() {
        $url = "services/" . $this->getId() . "/cloud_servers/user";
        $users = $this->getApi()->dataGet($url);
        if (isset($users['users']['users'])) return $users['users']['users'];
        return [];
    }

    /**
     * Returns the password if it's still available.
     * After the password has been received it will
     * be permanently deleted from the Nitrado database.
     *
     * @return mixed
     */
    public function getInitialPassword() {
        $url = "services/" . $this->getId() . "/cloud_servers/password";
        $password = $this->getApi()->dataGet($url);

        if (isset($password['password'])) {
            return $password['password'];
        }

        return null;
    }

    /**
     * Boots a turned down server.
     *
     * @return bool
     */
    public function doBoot() {
        $url = "services/" . $this->getId() . "/cloud_servers/boot";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Sends a shutdown command via ACPI.
     *
     * @return bool
     */
    public function doShutdown() {
        $url = "services/" . $this->getId() . "/cloud_servers/shutdown";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Sends a reboot command via ACPI.
     *
     * @return bool
     */
    public function doReboot() {
        $url = "services/" . $this->getId() . "/cloud_servers/reboot";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * This method resets your server immediately.
     * This action might result in data loss.
     *
     * @return bool
     */
    public function doHardReset() {
        $url = "services/" . $this->getId() . "/cloud_servers/hard_reset";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Returns the noVNC console endpoint.
     *
     * @return array
     */
    public function getConsole() {
        $url = "services/" . $this->getId() . "/cloud_servers/console";
        return $this->getApi()->dataGet($url);
    }

    /**
     * Changes the PTR record of a specific IPv4 address.
     *
     * @param string $ip
     * @param string $hostname
     * @return bool
     */
    public function changePTRRecord($ip, $hostname) {
        $url = "services/" . $this->getId() . "/cloud_servers/ptr/" . $ip;
        $this->getApi()->dataPost($url, [
            'hostname' => $hostname
        ]);
        return true;
    }

    /**
     * Changes the hostname of the server.
     * If no hostname has been provided, it will be reset to default.
     *
     * @param string $hostname
     * @return bool
     */
    public function changeHostname($hostname = null) {
        $url = "services/" . $this->getId() . "/cloud_servers/hostname";
        $this->getApi()->dataPost($url, [
            'hostname' => $hostname
        ]);
        return true;
    }

    /**
     * Returns a full list with all available images.
     *
     * @param Nitrapi $nitrapi
     * @return array
     */
    public static function getAvailableImages(Nitrapi &$nitrapi) {
        $images = $nitrapi->dataGet('/information/cloud_servers/images');
        $imgs = [];
        foreach ($images['images'] as $image)
            $imgs[] = new Image($image['id'], $image['name'], $image['is_windows']);
        return $imgs;
    }

    /**
     * Returns the daily traffic usage of the last 30 days.
     *
     * @return array
     */
    public function getTrafficStatistics() {
        $url = "services/" . $this->getId() . "/cloud_servers/traffic";
        return $this->getApi()->dataGet($url)['traffic'];
    }

    /**
     * Returns the Cloud Server resources usages.
     *
     * @return array
     */
    public function getResources($time = '4h') {
        $url = "services/" . $this->getId() . "/cloud_servers/resources";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                'time' => $time
            ]
        ])['resources'];
    }

    /**
     * Triggers a reinstallation.
     * Optional you can pass a new image.
     *
     * DANGER! This deletes all your data!
     *
     * @param Image|null $image
     */
    public function doReinstall(Image $image = null) {
        $url = "services/" . $this->getId() . "/cloud_servers/reinstall";

        $data = [];
        if ($image instanceof Image) {
            if ($image->isWindows() && !$this->getDetails()->getHardwareInfo()['windows']) {
                throw new NitrapiException("You need to rent the windows option to install a windows image.");
            }

            $data['image_id'] = $image->getId();
        }

        $this->getApi()->dataPost($url, $data);
        return true;
    }
}