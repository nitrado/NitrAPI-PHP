<?php
    
require_once '../vendor/autoload.php';

try {
    $api = new \Nitrapi\Nitrapi("ZDJhOTU2YTY3NWU2ODFkYmE2MzUwMzk3OGZlZGI5YzNmYTVmODMzNWU5ZTQzNjQyMzE5ZmYxNjgzNmY1MjhmMA");

    /**
     * @var $service \Nitrapi\Services\Service
     */
    foreach ($api->getServices() as $service) {
        echo "Service " . $service->getId() . " started at " . $service->getStartDate()->format('Y-m-d H:i:s') . PHP_EOL;
    }

} catch(\Exception $e) {
    var_dump("API Error: " . $e->getMessage());
}
