<?php

namespace Nitrapi\Services;

use Nitrapi\Common\Exceptions\NitrapiServiceTypeNotFoundException;
use Nitrapi\Nitrapi;

class ServiceFactory
{
    public static function factory(Nitrapi $api, array $options = array()) {
        $data = $api->dataGet("services/" . $options['id'], null);

        $type = $data['service']['type'];
        if ($type == 'cloud_server') $type = 'CloudServer'; //todo make it more fancy

        $class = "Nitrapi\\Services\\" . ucfirst($type) . "s\\" . ucfirst($type);

        if (!class_exists($class)) {
            throw new NitrapiServiceTypeNotFoundException("Class for Type " . $type . " not found");
        }

        return new $class($api, $data['service']);
    }
}