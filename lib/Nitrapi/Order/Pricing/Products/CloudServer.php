<?php

namespace Nitrapi\Order\Pricing\Products;

use Nitrapi\Common\Exceptions\NitrapiErrorException;
use Nitrapi\Order\Pricing\PartPricing;
use Nitrapi\Services\CloudServers\Image;

class CloudServer extends PartPricing  {

    protected static $product = 'cloud_server';

    public function setImage(Image $image) {
        $this->additionals['image_id'] = $image->getId();
    }

    public function setHostname($hostname) {
        $this->additionals['hostname'] = $hostname;
    }

}