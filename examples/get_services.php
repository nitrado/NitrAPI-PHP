<?php

require_once '../vendor/autoload.php';

try {
    $api = new \Nitrapi\Nitrapi("NTc2OGMyMjg0NGMxMGY5MDVhOTE1ZjJmY2VjODY2NDIzOWJiMGYzZmU0ZTI1MWMwNmQzMDZhMGYzNGZmYjU4Yw");

    /**
     * @var $service \Nitrapi\Services\Service
     */

    foreach ($api->getServices() as $service) {
        var_dump($service->getStartDate());
    }

} catch(\Exception $e) {
    var_dump("Error: " . $e->getMessage());
}
