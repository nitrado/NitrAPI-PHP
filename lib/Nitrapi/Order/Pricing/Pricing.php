<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

abstract class Pricing implements PricingInterface {

    /**
     * @var Nitrapi
     */
    protected $nitrapi;

    /**
     * Cached prices
     *
     * @var array
     */
    protected $prices = null;

    /**
     * Will be overwritten by parents
     */
    protected $product = null;
    
    protected $additionals = [];

    /**
     * Location id
     *
     * @var int
     */
    protected $location_id;

    public function __construct(Nitrapi $nitrapi, $location_id)
    {
        $this->nitrapi = $nitrapi;
        $this->location_id = $location_id;
    }

    /**
     * Get full price list for specified product
     *
     * @return mixed
     */
    public function getPrices() {
        if (isset($this->prices[$this->location_id])) {
            return $this->prices[$this->location_id];
        }

        $this->prices[$this->location_id] = $this->nitrapi->dataGet("/order/pricing/" . $this->product, null, [
            'query' => [
                'location' => $this->location_id
            ]
        ])['prices'];

        return $this->prices[$this->location_id];
    }

    /**
     * Extends a existing service
     * 
     * @param Service $service
     * @param $rentalTime
     */
    public function getExtendPriceForService(Service &$service, $rentalTime) {
        return $this->prices[$this->location_id] = $this->nitrapi->dataGet("/order/pricing/service", null, [
            'query' => [
                'service_id' => $service->getId(),
                'rental_time' => $rentalTime
            ]
        ])['price'];
    }

    /**
     * Extends the specific service about the specific rental time
     *
     * @param Service $service
     * @param $rentalTime
     * @return bool
     */
    public function doExtendService(Service &$service, $rentalTime) {
        $price = $this->getExtendPriceForService($service, $rentalTime);
        $orderArray = [
            'price' => $price,
            'rental_time' => $rentalTime,
            'service_id' => $service->getId(),
            'method' => 'extend'
        ];

        $this->nitrapi->dataPost("order/order/" . $this->product, $orderArray);

        return true;
    }

    /**
     * Orders the specified service
     *
     * @param $rentalTime
     * @return bool
     */
    public function orderService($rentalTime) {
        if ($this instanceof PartPricing) {
            $this->checkDependencies();
            $orderArray = [
                'price' => $this->getPrice($rentalTime),
                'rental_time' => $rentalTime,
                'location' => $this->location_id,
                'parts' => $this->getParts(),
                'additionals' => $this->additionals
            ];
        } elseif ($this instanceof DimensionPricing) {
            $orderArray = [
                'price' => $this->getPrice($rentalTime),
                'rental_time' => $rentalTime,
                'location' => $this->location_id,
                'dimensions' => $this->getDimensions(),
                'additionals' => $this->additionals
            ];
        } else {
            throw new PricingException("Unknown pricing calculation type.");
        }

        $this->nitrapi->dataPost("order/order/" . $this->product, $orderArray);

        //if no exception appears, order was successful
        return true;
    }
}