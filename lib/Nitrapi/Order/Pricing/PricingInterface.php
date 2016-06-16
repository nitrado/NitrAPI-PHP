<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Services\Service;

interface PricingInterface {

    function getPrice($rentalTime, Service &$service = null);
}