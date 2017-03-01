<?php

namespace Nitrapi\SSHKeys;

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
     */
    public function getPublicKeys()
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
     * @param $key
     * @param bool $enabled
     * @return bool
     */
    public function uploadPublicKey($key, $enabled = true)
    {
        $url = "user/ssh_keys";
        $this->api->dataPost($url, [
            'key' => $key,
            'enabled' => ($enabled ? 'true' : 'false')
        ]);

        return true;
    }

}