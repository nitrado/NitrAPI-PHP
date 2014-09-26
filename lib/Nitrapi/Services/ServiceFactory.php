<?php

namespace Nitrapi\Services;

use Nitrapi\Common\Exceptions\NitrapiServiceTypeNotFoundException;
use Nitrapi\Nitrapi;

class ServiceFactory
{
    public static function factory(Nitrapi $api, array $options = []) {
        $data = $api->dataGet("services/" . $options['id'], null, $options);

        $type = $data['type'];
        $class = "Nitrapi\\Services\\" . ucfirst($type) . "s\\" . ucfirst($type);

        if (!class_exists($class)) {
            throw new NitrapiServiceTypeNotFoundException("Class for Type " . $type . " not found");
        }

        return new $class($api, $data);
    }
}