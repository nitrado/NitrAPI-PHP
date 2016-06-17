<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

abstract class Pricing implements PricingInterface {

    protected static $product = null;

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

    protected $additionals = [];

    /**
     * Location id
     *
     * @var int
     */
    protected $locationId;

    public function __construct(Nitrapi &$nitrapi, $locationId)
    {
        $this->nitrapi = $nitrapi;
        $this->locationId = $locationId;
    }

    /**
     * Changes the location id
     *
     * @param $locationId
     */
    public function setLocationId($locationId) {
        $this->locationId = $locationId;
    }

    /**
     * @param Nitrapi $nitrapi
     * @return mixed
     */
    public static function getLocations(Nitrapi &$nitrapi) {
        if (static::$product === null) {
            throw new PricingException("You can not use the Pricing() class. Please use a product class.");
        }
        $locations = $nitrapi->dataGet("/order/order/locations")['locations'];
        foreach ($locations as $key => $location) {
            if (!isset($location['products'][static::$product])) unset($locations[$key]);
            if ($location['products'][static::$product] !== true) unset($locations[$key]);
        }

        return $locations;
    }
    
    /**
     * Get full price list for specified product
     *
     * @return mixed
     */
    public function getPrices(Service &$service = null) {
        $cacheName = $this->locationId;
        if ($service instanceof Service) $cacheName .= "/" . $service->getId();
        if (isset($this->prices[$cacheName])) {
            return $this->prices[$cacheName];
        }

        $query = [
            'location' => $this->locationId
        ];

        if ($service instanceof Service) {
            $query['sale_service'] = $service->getId();
        }

        $this->prices[$cacheName] = $this->nitrapi->dataGet("/order/pricing/" . $this->getProduct(), null, [
            'query' => $query
        ])['prices'];

        return $this->prices[$cacheName];
    }

    /**
     * Returns the price for extending a specific service
     * 
     * @param Service $service
     * @param $rentalTime
     */
    public function getExtendPriceForService(Service &$service, $rentalTime) {
        return $this->prices[$this->locationId] = $this->nitrapi->dataGet("/order/pricing/" . $this->getProduct(), null, [
            'query' => [
                'method' => 'extend',
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

        $this->nitrapi->dataPost("order/order/" . $this->getProduct(), $orderArray);

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
                'location' => $this->locationId,
                'parts' => $this->getParts(),
                'additionals' => $this->additionals
            ];
        } elseif ($this instanceof DimensionPricing) {
            $orderArray = [
                'price' => $this->getPrice($rentalTime),
                'rental_time' => $rentalTime,
                'location' => $this->locationId,
                'dimensions' => $this->getDimensions(),
                'additionals' => $this->additionals
            ];
        } else {
            throw new PricingException("Unknown pricing calculation type.");
        }

        $this->nitrapi->dataPost("order/order/" . $this->getProduct(), $orderArray);

        //if no exception appears, order was successful
        return true;
    }

    /**
     * Returns the price for swichting
     * 
     * @param $rentalTime
     * @param Service $service
     * @return mixed
     */
    public function getSwitchPrice($rentalTime, Service &$service) {
        return $this->getPrice($rentalTime, $service);
    }

    /**
     * Switches the product of a specific service
     *
     * @param Service $service
     * @param $rentalTime
     * @return bool
     */
    public function switchService($rentalTime, Service &$service) {
        if ($this instanceof PartPricing) {
            $this->checkDependencies();
            $orderArray = [
                'price' => $this->getSwitchPrice($rentalTime, $service),
                'rental_time' => $rentalTime,
                'location' => $this->locationId,
                'parts' => $this->getParts(),
                'additionals' => $this->additionals,
                'method' => 'switch',
                'service_id' => $service->getId(),
            ];
        } elseif ($this instanceof DimensionPricing) {
            $orderArray = [
                'price' => $this->getSwitchPrice($rentalTime, $service),
                'rental_time' => $rentalTime,
                'location' => $this->locationId,
                'dimensions' => $this->getDimensions(),
                'additionals' => $this->additionals,
                'method' => 'switch',
                'service_id' => $service->getId(),
            ];
        } else {
            throw new PricingException("Unknown pricing calculation type.");
        }

        $this->nitrapi->dataPost("order/order/" . $this->getProduct(), $orderArray);

        //if no exception appears, order was successful
        return true;
    }

    protected function getProduct() {
        return static::$product;
    }
}