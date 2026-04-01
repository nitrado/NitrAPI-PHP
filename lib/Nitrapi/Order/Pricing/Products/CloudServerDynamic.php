<?php

namespace Nitrapi\Order\Pricing\Products;

use Nitrapi\Order\Pricing\PartPricing;
use Nitrapi\Services\CloudServers\Image;

class CloudServerDynamic extends PartPricing  {
    protected static $product = 'cloud_server_dynamic';

    public function setImage(Image $image): void
    {
        $this->additionals['image_id'] = $image->getId();
    }

    public function setHostname($hostname): void
    {
        $this->additionals['hostname'] = $hostname;
    }
}
