<?php

namespace Nitrapi\Services\CloudServers;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Common\Exceptions\NitrapiServiceNotActiveException;
use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;
use Nitrapi\Services\SupportAuthorization;

class CloudServer extends Service
{
    protected $game;
    protected $info;

    /**
     * CloudServer constructor.
     *
     * @throws NitrapiHttpErrorException
     * @throws NitrapiServiceNotActiveException|NitrapiException
     */
    public function __construct(Nitrapi $api, &$data)
    {
        parent::__construct($api, $data);

        if (!$this->refresh()) {
            throw new NitrapiHttpErrorException('Received invalid data from NitrAPI.');
        }
    }

    /**
     * @throws NitrapiServiceNotActiveException|NitrapiException
     *
     * @see Gameserver::refresh()
     * @see Service::forceAction()
     */
    public function refresh(): bool
    {
        if (self::$ensureActiveService && $this->getStatus() !== self::SERVICE_STATUS_ACTIVE) {
            throw new NitrapiServiceNotActiveException('Service is not active any more.');
        }

        if (in_array($this->getStatus(), [self::SERVICE_STATUS_ACTIVE, self::SERVICE_STATUS_SUSPENDED], true)) {
            $url = 'services/' . $this->getId() . '/cloud_servers';
            $res = $this->getApi()->dataGet($url);
            if ($res !== null) {
                $this->info = $res;
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Return information about the Cloud Server.
     */
    public function getDetails(): CloudServerDetails
    {
        return new CloudServerDetails($this->info['cloud_server']);
    }

    /**
     * List all the users (with groups) on a Cloud Server. This users
     * are located in the /etc/passwd. All newly creates users on the
     * system are included in this array.
     *
     * @throws NitrapiException
     */
    public function getUsers(): array
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/user';
        $users = $this->getApi()->dataGet($url);
        return $users['users']['users'] ?? [];
    }

    /**
     * Returns the password if it's still available.
     * After the password has been received it will
     * be permanently deleted from the Nitrado database.
     *
     * @throws NitrapiException
     */
    public function getInitialPassword(): ?string
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/password';
        $password = $this->getApi()->dataGet($url);

        return $password['password'] ?? null;
    }

    /**
     * Boots a turned down server.
     *
     * @throws NitrapiException
     */
    public function doBoot(): string
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/boot';
        return $this->getApi()->dataPost($url);
    }

    /**
     * Sends a shutdown command via ACPI.
     *
     * @throws NitrapiException
     */
    public function doShutdown(): string
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/shutdown';
        return $this->getApi()->dataPost($url);
    }

    /**
     * Sends a reboot command via ACPI.
     *
     * @throws NitrapiException
     */
    public function doReboot(): string
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/reboot';
        return $this->getApi()->dataPost($url);
    }

    /**
     * This method resets your server immediately.
     * This action might result in data loss.
     *
     * @throws NitrapiException
     */
    public function doHardReset(): string
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/hard_reset';
        return $this->getApi()->dataPost($url);
    }

    /**
     * This method reboots your server in rescue mode.
     * This action might result in data loss.
     *
     * @throws NitrapiException
     */
    public function doRescue(): string
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/rescue';
        return $this->getApi()->dataPost($url);
    }

    /**
     * This method leaves the rescue mode and reboots the server.
     * This action might result in data loss.
     *
     * @throws NitrapiException
     */
    public function doUnrescue(): string
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/unrescue';
        return $this->getApi()->dataPost($url);
    }

    /**
     * Returns the noVNC console endpoint.
     *
     * @throws NitrapiException
     */
    public function getConsole(): array
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/console';
        return $this->getApi()->dataGet($url);
    }

    /**
     * Changes the PTR record of a specific IPv4 address.
     *
     * @throws NitrapiException
     */
    public function changePTRRecord(string $ip, string $hostname): bool
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/ptr/' . $ip;
        $this->getApi()->dataPost($url, [
            'hostname' => $hostname,
        ]);
        return true;
    }

    /**
     * Changes the hostname of the server.
     * If no hostname has been provided, it will be reset to default.
     *
     * @throws NitrapiException
     */
    public function changeHostname(?string $hostname = null): bool
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/hostname';
        $this->getApi()->dataPost($url, [
            'hostname' => $hostname,
        ]);
        return true;
    }

    /**
     * Returns a full list with all available images.
     *
     * @param Nitrapi $nitrapi
     * @return array
     * @throws NitrapiException
     */
    public static function getAvailableImages(Nitrapi $nitrapi): array
    {
        $images = $nitrapi->dataGet('/information/cloud_servers/images');
        $imgs = [];
        foreach ($images['images'] as $image) {
            $imgs[] = new Image(
                $image['id'],
                $image['name'],
                $image['is_windows'],
                $image['default'],
                $image['has_daemon'],
                $image['is_daemon_compatible'],
            );
        }
        return $imgs;
    }

    /**
     * Returns the daily traffic usage of the last 30 days.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getTrafficStatistics(): array
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/traffic';
        return $this->getApi()->dataGet($url)['traffic'];
    }

    /**
     * Returns the Cloud Server resources usages.
     *
     * @param string $time
     * @return array
     * @throws NitrapiException
     */
    public function getResources(string $time = '4h'): array
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/resources';
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                'time' => $time,
            ],
        ])['resources'];
    }

    /**
     * Triggers a reinstallation.
     * Optional you can pass a new image.
     *
     * DANGER! This deletes all your data!
     *
     * @param Image|null $image
     * @return bool
     * @throws NitrapiException
     */
    public function doReinstall(?Image $image = null): bool
    {
        $url = 'services/' . $this->getId() . '/cloud_servers/reinstall';

        $data = [];
        if ($image instanceof Image) {
            if ($image->isWindows() && !$this->getDetails()->getHardwareInfo()['windows']) {
                throw new NitrapiException('You need to rent the windows option to install a windows image.');
            }

            $data['image_id'] = $image->getId();
        }

        $this->getApi()->dataPost($url, $data);
        return true;
    }

    /**
     * @throws NitrapiException
     */
    public function getSupportAuthorization(): ?SupportAuthorization
    {
        $url = 'services/' . $this->getId() . '/support_authorization';

        try {
            $nitrapi = $this->getApi();
            $result = new SupportAuthorization($nitrapi, $nitrapi->dataGet($url));
        } catch (NitrapiHttpErrorException $e) {
            // No SupportAuthorization exists
            $result = null;
        }

        return $result;
    }

    /**
     * @throws NitrapiException
     */
    public function createSupportAuthorization(): SupportAuthorization
    {
        $url = 'services/' . $this->getId() . '/support_authorization';

        $nitrapi = $this->getApi();
        return new SupportAuthorization($nitrapi, $nitrapi->dataPost($url));
    }

    /**
     * @throws NitrapiException
     */
    public function deleteSupportAuthorization(): bool
    {
        $url = 'services/' . $this->getId() . '/support_authorization';

        try {
            $this->getApi()->dataDelete($url);
        } catch (NitrapiHttpErrorException $e) {
            return false;
        }

        return true;
    }
}
