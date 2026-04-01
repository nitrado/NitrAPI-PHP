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

    public function __construct(Nitrapi $api)
    {
        $this->api = $api;
    }

    /**
     * Returns an array with all available top-level domains and prices.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getPricing(): array
    {
        return $this->api->dataGet('/domain/pricing');
    }

    /**
     * Returns a list with all active domains.
     *
     * @return Domain[]
     * @throws NitrapiException
     */
    public function getDomains(): array
    {
        $domains = [];

        foreach ($this->api->dataGet('/domain')['domains'] as $domain) {
            $domains[] = new Domain($this->api, $domain);
        }

        return $domains;
    }

    /**
     * Returns a single Domain object by domain or ID.
     *
     * @param mixed $domain
     * @return Domain
     * @throws NitrapiException
     */
    public function getDomain($domain): Domain
    {
        foreach ($this->api->dataGet('/domain')['domains'] as $_domain) {
            if ($domain === $_domain['domain'] || (preg_match(
                        "@^\d+$@",
                        $domain,
                    ) && $_domain['id'] === (int)$domain)) {
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
     * @param null|string $authCode
     * @return string
     * @throws NitrapiException
     */
    public function registerDomain(Domain $domain, Handle $handle, ?string $authCode = null): string
    {
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
