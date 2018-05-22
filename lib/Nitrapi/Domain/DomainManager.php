<?php

namespace Nitrapi\Domain;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class DomainManager
{
    /**
     * @var $api Nitrapi
     */
    protected $api;

    public function __construct(Nitrapi &$api) {
        $this->api = $api;
    }

    /**
     * Returns an array with all available top-level domains and prices.
     *
     * @return array
     */
    public function getPricing() {
        return $this->api->dataGet('/domain/pricing');
    }

    /**
     * Returns a list with all active domains.
     *
     * @return Domain[]
     */
    public function getDomains() {
        $domains = [];

        foreach ($this->api->dataGet('/domain')['domains'] as $domain) {
            $domains[] = new Domain($this->api, $domain);
        }

        return $domains;
    }

    /**
     * Returns a single Domain object.
     *
     * @param $domain
     * @return Domain
     */
    public function getDomain($domain) {
        foreach ($this->api->dataGet('/domain')['domains'] as $_domain) {
            if ($_domain['domain'] === $domain) {
                return new Domain($this->api, $_domain);
            }
        }

        throw new NitrapiException("Domain " . $domain . " can't be found");
    }

    /**
     * Registers a new Domain on your account.
     *
     * @param Domain $domain
     * @param Handle $handle
     * @param null $authCode
     * @return string
     */
    public function registerDomain(Domain $domain, Handle $handle, $authCode = null) {
        $data = [
            'owner_c' => $handle->getHandle(),
            'admin_c' => $handle->getHandle(),
        ];

        if (!empty($authCode)) {
            $data['auth_code'] = $authCode;
        }

        return $this->api->dataPost('/domain/' . $domain->getDomain() . '/order', $data);
    }

}