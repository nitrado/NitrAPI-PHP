<?php

namespace Nitrapi\Geo;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class CountryManager
{
    /**
     * @var $api Nitrapi
     */
    protected $api;

    public function __construct(Nitrapi &$api) {
        $this->api = $api;
    }

    /**
     * Returns all tlds
     *
     * @param bool $show_disabled show disabled tlds
     * @param string $provider limit to one provider
     * @return TopLevelDomain[]
     */
    public function getTlds($show_disabled = false, $provider = NULL) {
        $tlds = [];
        $data = [
            "query" => [
                "show_disabled" => $show_disabled ? 'true' : 'false',
            ]
        ];
        if (!is_null($provider)) {
            $data['provider'] = $provider;
        }

        foreach ($this->api->dataGet("/tlds", null, $data) as $tld) {
            $tlds[] = new TopLevelDomain($this->api, $tld);
        }

        return $tlds;
    }

    /**
     * Returns all country codes and the corresponding countries
     *
     * @return array list that matches 2 character country codes to the countries name
     */
    public function getCountrycodes() {
        return $this->api->dataGet("/geo/countrycodes");
    }

}
