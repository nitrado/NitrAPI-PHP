<?php

namespace Nitrapi\Order\Pricing;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;
use Nitrapi\Services\Service;

abstract class Pricing implements PricingInterface
{
    protected static $product;

    /**
     * @var Nitrapi
     */
    protected $nitrapi;

    /**
     * Cached prices
     *
     * @var array|null
     */
    protected $prices;

    /**
     * Currency for price calculation
     *
     * @var array|null
     */
    protected $currency;

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

    public function __construct(Nitrapi $nitrapi, Location $location)
    {
        $this->nitrapi = $nitrapi;
        $this->locationId = $location->getId();
    }

    /**
     * This can be used only for pricing information
     *
     * @var $currency
     */
    public function setCurrency($currency = null): void
    {
        $this->currency = $currency;
    }

    /**
     * Changes the location id
     *
     * @param $locationId
     */
    public function setLocationId($locationId): void
    {
        $this->locationId = $locationId;
    }

    /**
     * @param Nitrapi $nitrapi
     * @return array
     * @throws NitrapiException
     * @throws PricingException
     */
    public static function getLocations(Nitrapi $nitrapi): array
    {
        if (static::$product === null) {
            throw new PricingException("You can not use the Pricing() class. Please use a product class.");
        }
        $_locations = $nitrapi->dataGet("/order/order/locations")['locations'];
        $locations = [];
        foreach ($_locations as $location) {
            if (!isset($location['products'][static::$product])) {
                continue;
            }
            if ($location['products'][static::$product] !== true) {
                continue;
            }
            $locations[] = new Location($location);
        }

        return $locations;
    }

    /**
     * @param Nitrapi $nitrapi
     * @param int $id
     * @return Location
     * @throws NitrapiException
     * @throws PricingException
     */
    public static function getLocation(Nitrapi $nitrapi, int $id): Location
    {
        if (static::$product === null) {
            throw new PricingException("You can not use the Pricing() class. Please use a product class.");
        }
        $_locations = $nitrapi->dataGet("/order/order/locations")['locations'];
        foreach ($_locations as $location) {
            if (!isset($location['products'][static::$product])) {
                continue;
            }
            if ($location['products'][static::$product] !== true) {
                continue;
            }
            if ($location['id'] !== $id) {
                continue;
            }
            return new Location($location);
        }

        throw new PricingException("Location " . $id . " not found or product not supported");
    }

    /**
     * Get full price list for specified product
     *
     * @return mixed
     * @throws NitrapiException
     */
    public function getPrices(?Service $service = null)
    {
        $cacheName = $this->locationId . "/" . $this->currency;
        if ($service instanceof Service) {
            $cacheName .= "/" . $service->getId();
        }
        if (isset($this->prices[$cacheName])) {
            return $this->prices[$cacheName];
        }

        $query = [
            'location' => $this->locationId,
        ];

        if ($service instanceof Service) {
            $query['sale_service'] = $service->getId();
        }

        if (!empty($this->currency) && (!($service instanceof Service))) {
            $query['currency'] = $this->currency;
        }

        $this->prices[$cacheName] = $this->nitrapi->dataGet("/order/pricing/" . $this->getProduct(), null, [
            'query' => $query,
        ])['prices'];

        return $this->prices[$cacheName];
    }

    /**
     * Returns the price for extending a specific service
     *
     * @param Service $service
     * @param $rentalTime
     * @return mixed
     * @throws NitrapiException
     */
    public function getExtendPriceForService(Service $service, $rentalTime)
    {
        $this->setCurrency(null); //use user currency
        return $this->prices[$this->locationId] = $this->nitrapi->dataGet(
            "/order/pricing/" . $this->getProduct(),
            null,
            [
                'query' => [
                    'method' => 'extend',
                    'service_id' => $service->getId(),
                    'rental_time' => $rentalTime,
                ],
            ],
        )['extend']['prices'][$rentalTime];
    }

    /**
     * Extends the specific service about the specific rental time
     *
     * @param Service $service
     * @param $rentalTime
     * @return int The service's service_id
     * @throws NitrapiException
     */
    public function extendService(Service $service, $rentalTime): int
    {
        $this->setCurrency(null); //use user currency
        $price = $this->getExtendPriceForService($service, $rentalTime);
        $orderArray = [
            'price' => $price,
            'rental_time' => $rentalTime,
            'service_id' => $service->getId(),
            'method' => 'extend',
        ];

        $this->nitrapi->dataPost("order/order/" . $this->getProduct(), $orderArray);

        return $service->getId();
    }

    /**
     * Orders the specified service
     *
     * @param $rentalTime
     * @return int The new service's service_id
     * @throws NitrapiException
     */
    public function orderService($rentalTime): int
    {
        $this->setCurrency(null); //use user currency

        $orderArray = $this->getNewOrderArray($rentalTime);

        $result = $this->nitrapi->dataPost("order/order/" . $this->getProduct(), $orderArray);

        //if no exception appears, order was successful
        return $result['order']['service_id'];
    }

    /**
     * Returns the price for swichting
     *
     * @param Service $service
     * @param $rentalTime
     * @return int
     * @throws PricingException
     */
    public function getSwitchPrice(Service $service, $rentalTime): int
    {
        $this->setCurrency(null); //use user currency
        return $this->getPrice($rentalTime, $service);
    }

    /**
     * Switches the product of a specific service
     *
     * @param Service $service
     * @param $rentalTime
     * @return int The service's service_id
     * @throws NitrapiException
     */
    public function switchService(Service $service, $rentalTime): int
    {
        $this->setCurrency(null); //use user currency
        $orderArray = $this->getSwitchOrderArray($service, $rentalTime);

        $result = $this->nitrapi->dataPost("order/order/" . $this->getProduct(), $orderArray);

        //if no exception appears, order was successful
        return $result['order']['service_id'];
    }

    /**
     * Removes X% of the advice if the advice is higher than the price.
     *
     * @param int $price
     * @param int $advice
     * @param float $removePercent
     * @return int
     */
    protected function calcAdvicePrice(int $price, int $advice, float $removePercent): int
    {
        if ($advice > $price) {
            $advice -= round(($advice - $price) * ($removePercent / 100));
        }

        return ($price - $advice);
    }

    protected function getProduct()
    {
        return static::$product;
    }

    abstract protected function getNewOrderArray($rentalTime): array;

    abstract protected function getSwitchOrderArray(Service $service, $rentalTime): array;
}
