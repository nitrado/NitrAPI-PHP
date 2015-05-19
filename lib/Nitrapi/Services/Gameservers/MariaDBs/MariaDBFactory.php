<?php

namespace Nitrapi\Services\Gameservers\MariaDBs;

use Nitrapi\Services\Gameservers\Gameserver;

class MariaDBFactory
{
    public static function factory(Gameserver &$service, &$id) {
        $data = $service->getApi()->dataGet("services/" . $service->getId() . "/gameservers/mariadbs/" . $id);

        return new MariaDB($service, $data['database']);
    }
}