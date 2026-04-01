<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Services\Service;

interface PricingInterface {
    /**
     * @param $rentalTime
     * @param Service|null $service
     * @return int
     * @throws PricingException
     */
    public function getPrice($rentalTime, ?Service $service = null): int;
}
