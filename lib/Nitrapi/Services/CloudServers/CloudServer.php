<?php

namespace Nitrapi\Services\CloudServers;

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
     * Returns informations about the gameserver
     *
     * @return CloudServerDetails
     */
    public function getDetails() {
        return new CloudServerDetails($this->info['cloud_server']);
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

    public static function getAvailableImages(Nitrapi &$nitrapi) {
        $images = $nitrapi->dataGet('/information/cloud_servers/images');
        $imgs = [];
        foreach ($images['images'] as $image)
            $imgs[] = new Image($image['id'], $image['name'], $image['is_windows']);
        return $imgs;
    }
}