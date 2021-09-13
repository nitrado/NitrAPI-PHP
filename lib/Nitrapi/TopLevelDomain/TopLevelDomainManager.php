<?php

namespace Nitrapi\TopLevelDomain;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class TopLevelDomainManager
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
     * @param bool $show_disabled show disabled tlds (that can no longer be bought)
     * @param string $provider limit to one provider (one of cps, nicdirect)
     * @return TopLevelDomain[]
     */
    public function getTlds($show_disabled = false, $provider = NULL) {
        $tlds = [];
        $data = [
            "show_disabled" => $show_disabled ? 'true' : 'false',
        ];
        if (!is_null($provider)) {
            $data['provider'] = $provider;
        }

        foreach ($this->api->dataGet("/tlds", $data) as $tld) {
            $tlds[] = new TopLevelDomain($this->api, $tld);
        }

        return $tlds;
    }

}
