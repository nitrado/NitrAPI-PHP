<?php

namespace Nitrapi\Services\Gameservers\Addons;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Gameservers\Gameserver;

class AddonManager {
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver &$service) {
        $this->service = $service;
    }

    public function availableAddons() {
        try {
            $url = "/services/".$this->service->getId()."/gameservers/addons/";
            $addons = $this->service->getApi()->dataGet($url);
        } catch (NitrapiException $e) {
            throw new NitrapiException($e->getMessage());
        }

        if (!is_array($addons))
            throw new NitrapiException($addons);

        $addonInstances = [];
        foreach ($addons['addons'] as $addon => $params)
            $addonInstances[] = new Addon($addon, $params['description'], $params['status']);
        return $addonInstances;
    }
}
