<?php

namespace Nitrapi\Services\Gameservers\Packages;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Gameservers\Gameserver;

class PackageManager {
    /**
     * @var Gameserver $service
     */
    protected $service;

    public function __construct(Gameserver $service) {
        $this->service = $service;
    }

    public function availablePackages() {
        try {
            $url = "/services/".$this->service->getId()."/gameservers/packages/";
            $packages = $this->service->getApi()->dataGet($url);
        } catch (NitrapiException $e) {
            throw new NitrapiException($e->getMessage());
        }

        if (!is_array($packages))
            throw new NitrapiException($packages);

        $packageInstances = [];
        foreach ($packages['packages'] as $package => $params)
            $packageInstances[] = new Package($this->service, $package, $params['description'], $params['status'], $params['version'], $params['patches'], $params['dependencies']);
        return $packageInstances;
    }
}
