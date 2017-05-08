<?php

namespace Nitrapi\OAuth;

use Nitrapi\Nitrapi;

class Client {

    /**
     * @var Nitrapi
     */
    private $api;

    /**
     * @var array
     */
    private $data;

    public function __construct(Nitrapi $api, array $data) {
        $this->api = $api;
        $this->data = $data;
    }

    public function getClientId() {
        return $this->data['id'];
    }

    /**
     * Only available if client has been created.
     *
     * @return string|null
     */
    public function getClientSecret() {
        return $this->data['secret'];
    }

    /**
     * Returns true if the client is a official Nitrado.net Client.
     *
     * @return bool
     */
    public function isOfficial() {
        return $this->data['official'];
    }

    /**
     * Returns false if the client has been locked by the Nitrado.net staff.
     *
     * @return bool
     */
    public function isEnabled() {
        return $this->data['enabled'];
    }

    /**
     * Returns the name of the client.
     *
     * @return string
     */
    public function getName() {
        return $this->data['name'];
    }

    /**
     * Returns a base64 encoded icon if available.
     *
     * @return string
     */
    public function getIcon() {
        return $this->data['icon'];
    }

    /**
     * Returns the client description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->data['description'];
    }

    /**
     * Returns the client email address.
     *
     * @return string
     */
    public function getEMail() {
        return $this->data['email'];
    }

    /**
     * Returns all valid redirect uris for this client.
     *
     * @return array
     */
    public function getRedirectURIs() {
        return (array)$this->data['redirect_uris'];
    }

    /**
     * Returns all allowed grant types for this client.
     *
     * @return array
     */
    public function getGrantTypes() {
        return (array)$this->data['grant_types'];
    }

    /**
     * Updates the client.
     *
     * @param array $data
     */
    public function update(array $data) {
        $this->data = $this->api->dataPut('/oauth/' . $this->getClientId(), $data)['client'];
    }

    /**
     * Returns a new client secret and invalidates the old one!
     *
     * @param $updateToken
     * @return string
     */
    public function renewSecret($updateToken) {
        return $this->api->dataPut('/oauth/' . $this->getClientId() . '/secret', [
            'token' => $updateToken
        ])['client']['secret'];
    }

    /**
     * Deletes the client!
     *
     * This invalidates all access and refresh tokens!
     *
     * @param $updateToken
     * @return bool
     */
    public function delete($updateToken) {
        return $this->api->dataDelete('/oauth/' . $this->getClientId(), [
            'token' => $updateToken
        ]);
    }

}