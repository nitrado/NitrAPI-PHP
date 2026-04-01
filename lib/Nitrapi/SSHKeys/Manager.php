<?php

namespace Nitrapi\SSHKeys;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class Manager
{
    /**
     * @var Nitrapi
     */
    private $api;

    public function __construct(Nitrapi $api)
    {
        $this->api = $api;
    }

    /**
     * Returns all your SSH Public Keys
     *
     * @return array
     * @throws NitrapiException
     */
    public function getPublicKeys(): array
    {
        $url = "user/ssh_keys";
        $keys = [];
        foreach ($this->api->dataGet($url)['keys'] as $key) {
            $keys[] = new SSHKey($this->api, $key);
        }
        return $keys;
    }

    /**
     * Uploads a new SSH Public Key
     *
     * @throws NitrapiException
     */
    public function uploadPublicKey($key, bool $enabled = true): bool
    {
        $url = "user/ssh_keys";
        $this->api->dataPost($url, [
            'key' => $key,
            'enabled' => ($enabled ? 'true' : 'false'),
        ]);

        return true;
    }
}
