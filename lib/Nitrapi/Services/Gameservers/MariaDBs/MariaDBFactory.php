<?php

namespace Nitrapi\Services\Gameservers\MariaDBs;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Services\Gameservers\Gameserver;

class MariaDBFactory
{
    /**
     * @throws NitrapiException
     */
    public static function factory(Gameserver $service, &$id): MariaDB
    {
        $data = $service->getApi()->dataGet("services/" . $service->getId() . "/gameservers/mariadbs/" . $id);

        return new MariaDB($service, $data['database']);
    }
}
