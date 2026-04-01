<?php

namespace Nitrapi\OAuth;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class OAuth
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
     * Returns a specific OAuth 2.0 Client by id.
     *
     * @param $id
     * @return Client
     * @throws NitrapiException
     */
    public function getClient($id): Client
    {
        $_client = $this->api->dataGet('/oauth/' . $id)['client'];
        return new Client($this->api, $_client);
    }

    /**
     * Returns all your OAuth 2.0 clients.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getClients(): array
    {
        $clients = [];
        $_clients = $this->api->dataGet('/oauth')['clients'];

        foreach ($_clients as $_client) {
            $clients[] = new Client($this->api, $_client);
        }

        return $clients;
    }

    /**
     * Creates a new OAuth 2.0 client.
     *
     * @param array $data
     * @return Client
     * @throws NitrapiException
     */
    public function createClient(array $data): Client
    {
        $_client = $this->api->dataPost('/oauth/', $data)['client'];
        return new Client($this->api, $_client);
    }
}
