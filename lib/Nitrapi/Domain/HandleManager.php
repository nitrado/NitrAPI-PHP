<?php

namespace Nitrapi\Domain;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class HandleManager
{
    /**
     * @var $api Nitrapi
     */
    protected $api;

    public const TYPE_OWNER_C = 'owner_c';
    public const TYPE_ADMIN_C = 'admin_c';
    public const TYPE_TECH_C = 'tech_c';

    public function __construct(Nitrapi $api)
    {
        $this->api = $api;
    }

    /**
     * Returns a array with all handles for your user.
     *
     * @return Handle[]
     * @throws NitrapiException
     */
    public function getHandles(): array
    {
        $handles = [];

        foreach ($this->api->dataGet('/domain/contact')['contacts'] as $contact) {
            $handles[] = new Handle($this->api, $contact);
        }

        return $handles;
    }

    /**
     * Returns a single handle object.
     *
     * @param $handle
     * @return Handle
     * @throws NitrapiException
     */
    public function getHandle($handle): Handle
    {
        foreach ($this->api->dataGet('/domain/contact')['contacts'] as $contact) {
            if ($contact['handle'] === $handle) {
                return new Handle($this->api, $contact);
            }
        }

        throw new NitrapiException("Handle " . $handle . " can't be found");
    }
}
