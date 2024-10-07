<?php

namespace Nitrapi\Services\Gameservers;

use Nitrapi\Services\Service;

class CurseforgeWorkshop {
    protected $service;

    public function __construct(Service $service) {
        $this->service = $service;
    }

    public function searchMods($searchString, $page = 0, $sortBy = 'popularity') {
        $url = $this->baseEndpointUrl() . "/search_mods";
        $query = [
            'page' => $page,
            'sort_by' => $sortBy
        ];
        if (!empty($searchString)) {
            $query['search'] = $searchString;
        }
        return $this->service->getApi()->dataGet($url, null, ['query' => $query]);
    }

    public function getInstalledMods() {
        $url = $this->baseEndpointUrl() . "/installed_mods";
        return $this->service->getApi()->dataGet($url);
    }

    private function baseEndpointUrl() {
        return "/services/".$this->service->getId()."/gameservers/curseforge_workshop";
    }
}