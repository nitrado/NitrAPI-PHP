<?php

namespace Nitrapi\Customer;

use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Nitrapi;

class Customer {
    private $api;
    private $data;

    public function __construct(Nitrapi $api, $token) {
        $this->api = $api;
        $this->data = $api->dataGet("user?access_token=" . $token, null);
    }

    /**
     * Returns the user id.
     * @return string
     */
    public function getUserId() {
        return $this->get('user_id');
    }

    /**
     * Returns the username.
     * @return string username
     */
    public function getUsername() {
        return $this->get('username');
    }

    /**
     * Returns credit.
     * @return int
     */
    public function getCredit() {
        return $this->get('credit');
    }

    /**
     * Returns email address of the user.
     * @return string
     */
    public function getEmail() {
        return $this->get('email');
    }

    /**
     * Returns the personal details of the user.
     * @return array
     */
    public function getPersonalData() {
        return $this->get('profile');
    }

    /**
     * This function returns the whole data-set
     * @return mixed
     */
    public function get($field = null) {
        if($field === null) return $this->data['user'];
        return $this->data['user'][$field];
    }

    /**
     * Returns a webinterface token
     *
     * @return string
     */
    public function getWebinterfaceToken() {
        $token = $this->api->dataGet('user/webinterface_token');
        return $token['token']['token'];
    }

    /**
     * Deletes all webinterface tokens. This logs out all users from your webinterface.
     *
     * @return string
     */
    public function deleteWebinterfaceTokens() {
        try {
            $this->api->dataDelete('user/webinterface_token');
        } catch (NitrapiHttpErrorException $e) {
            return false;
        }

        return true;
    }
}