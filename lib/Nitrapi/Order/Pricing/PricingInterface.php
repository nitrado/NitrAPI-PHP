<?php

namespace Nitrapi\Order\Pricing;

interface PricingInterface {

    function getPrice($rentalTime);
}