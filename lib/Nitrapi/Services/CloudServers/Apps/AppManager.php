<?php

namespace Nitrapi\Services\CloudServers\Apps;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\CloudServers\CloudServer;

/**
 * Class AppManager
 *
 * @package Nitrapi\Services\CloudServers\Apps
 */
class AppManager
{
    /**
     * @var CloudServer $service
     */
    public $service;

    public function __construct(CloudServer $service)
    {
        $this->service = $service;
    }

    /**
     * Return all installed apps
     *
     * @return App[] all installed apps
     * @throws NitrapiException
     */
    public function getInstalledApps(): array
    {
        $url = '/services/' . $this->service->getId() . '/cloud_servers/apps';
        /* @var $apiResponse array[][] */
        $apiResponse = $this->service->getApi()->dataGet($url);

        $apps = [];
        foreach ($apiResponse['apps'] as $app) {
            $apps[] = new App($this, $app);
        }

        // Sort
        usort($apps, static function (App $a, App $b) {
            return strcmp(strtolower($a->getAppName()), strtolower($b->getAppName()));
        });

        return $apps;
    }

    /**
     * Return all app descriptions
     *
     * @return AppDescription[] All app descriptions
     * @throws NitrapiException
     */
    public function getAvailableAppDescriptions(): array
    {
        $url = '/services/' . $this->service->getId() . '/cloud_servers/apps/available';
        /* @var $apiResponse array[][] */
        $apiResponse = $this->service->getApi()->dataGet($url);

        $apps = [];
        foreach ($apiResponse['apps'] as $description) {
            $apps[] = new AppDescription($description);
        }
        return $apps;
    }
}
