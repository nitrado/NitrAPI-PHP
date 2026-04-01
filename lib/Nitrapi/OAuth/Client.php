<?php

namespace Nitrapi\OAuth;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class Client
{
    /**
     * @var Nitrapi
     */
    private $api;

    /**
     * @var array
     */
    private $data;

    public function __construct(Nitrapi $api, array $data)
    {
        $this->api = $api;
        $this->data = $data;
    }

    public function getClientId()
    {
        return $this->data['id'];
    }

    /**
     * Only available if client has been created.
     *
     * @return string|null
     */
    public function getClientSecret(): ?string
    {
        return $this->data['secret'];
    }

    /**
     * Returns true if the client is a official Nitrado.net Client.
     *
     * @return bool
     */
    public function isOfficial(): bool
    {
        return $this->data['official'];
    }

    /**
     * Returns false if the client has been locked by the Nitrado.net staff.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->data['enabled'];
    }

    /**
     * Returns the name of the client.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Returns a base64 encoded icon if available.
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->data['icon'];
    }

    /**
     * Returns the client description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->data['description'];
    }

    /**
     * Returns the client email address.
     *
     * @return string
     */
    public function getEMail(): string
    {
        return $this->data['email'];
    }

    /**
     * Returns the website.
     *
     * @return string
     */
    public function getWebsite(): string
    {
        return $this->data['website'];
    }

    /**
     * Returns all valid redirect uris for this client.
     *
     * @return array
     */
    public function getRedirectURIs(): array
    {
        return (array)$this->data['redirect_uris'];
    }

    /**
     * Returns all allowed grant types for this client.
     *
     * @return array
     */
    public function getGrantTypes(): array
    {
        return (array)$this->data['grant_types'];
    }

    /**
     * Updates the client.
     *
     * @param array $data
     * @throws NitrapiException
     */
    public function update(array $data): void
    {
        $this->data = $this->api->dataPut('/oauth/' . $this->getClientId(), $data)['client'];
    }

    /**
     * Returns a new client secret and invalidates the old one!
     *
     * @param $updateToken
     * @return string
     * @throws NitrapiException
     */
    public function renewSecret($updateToken): string
    {
        return $this->api->dataPut('/oauth/' . $this->getClientId() . '/secret', [
            'token' => $updateToken,
        ])['client']['secret'];
    }

    /**
     * Deletes the client!
     *
     * This invalidates all access and refresh tokens!
     *
     * @param $updateToken
     * @return bool
     * @throws NitrapiException
     */
    public function delete($updateToken): bool
    {
        return $this->api->dataDelete('/oauth/' . $this->getClientId(), [
            'token' => $updateToken,
        ]);
    }
}
