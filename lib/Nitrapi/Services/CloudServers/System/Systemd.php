<?php

namespace Nitrapi\Services\CloudServers\System;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\CloudServers\CloudServer;

/**
 * Class Systemd
 *
 * Implement all needed Systemd related endpoints, which can be used to
 * control the Systemd daemon, and list the available units. The unit list
 * is sorted.
 *
 * @package Nitrapi\Services\CloudServers\System
 */
class Systemd
{
    public $service;

    public function __construct(CloudServer $service)
    {
        $this->service = $service;
    }

    /**
     * Returns a SSE (server-send event) stream URL, which will stream
     * changes on the Systemd services.
     *
     * @param string|null $unit a unit to filter at
     * @param bool $initialState to send an initial state for all units
     * @return string The URL
     * @throws NitrapiException
     */
    public function changeFeedUrl(?string $unit = null, bool $initialState = false): string
    {
        /* @var $apiResults array[][] */
        $apiResult = $this->service->getApi()->dataGet(
            $this->url('/changefeed'),
            [
                'unit' => $unit,
                'initial_state' => $initialState,
            ],
        );
        return $apiResult['token']['url'];
    }

    /**
     * Reset all units in failure state back to normal.
     *
     * @return void
     * @throws NitrapiException
     */
    public function resetAllFailedUnits(): void
    {
        $this->service->getApi()->dataPost($this->url('/reset_all_failed'));
    }

    /**
     * Reload the Systemd daemon
     *
     * @return void
     * @throws NitrapiException
     */
    public function reloadDaemon(): void
    {
        $this->service->getApi()->dataPost($this->url('/daemon_reload'));
    }

    /**
     * Lists all the units Systemd manages. The resulting array will be sorted
     * by type and name.
     *
     * @return Unit[] the list of units
     * @throws NitrapiException
     */
    public function getUnits(): array
    {
        /* @var $apiResults array[][] */
        $apiResults = $this->service->getApi()->dataGet($this->url(''));
        $units = [];
        foreach ($apiResults['units'] as $data) {
            $units[] = new Unit($this, $data);
        }

        // Sort
        usort($units, static function (Unit $a, Unit $b) {
            $aType = explode('.', $a->getName());
            $bType = explode('.', $b->getName());

            $aType = end($aType);
            $bType = end($bType);

            if ($aType !== $bType) {
                return strcmp($aType, $bType);
            }

            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });

        return $units;
    }

    private function url($endpoint): string
    {
        return '/services/' . $this->service->getId() . '/cloud_servers/system/units' . $endpoint;
    }
}
