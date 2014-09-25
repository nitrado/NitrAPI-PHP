<?php

require_once '../vendor/autoload.php';

try {
    $api = new \Nitrapi\Nitrapi("<TOKEN>");

    /**
     * @var $service \Nitrapi\Services\Service
     */

    foreach ($api->getServices() as $service) {
        var_dump($service->getSuspendDate());
    }

} catch(\Exception $e) {
    var_dump("Error: " . $e->getMessage());
}
