<?php

namespace Nitrapi\Customer;

use Nitrapi\Nitrapi;

class TwoFactor
{

    private $api;

    public function __construct(Nitrapi $api)
    {
        $this->api = $api;
    }

    /**
     * Current Google Authenticator Information
     * Also contains Information to add new Phone.
     *
     * @param $updateToken
     * @return array
     */
    public function getGoogleAuthenticator($updateToken) {
        return $this->api->dataGet("user/two_factor/google", null, [
            'query' => [
                'token' => $updateToken
            ]
        ])['google'];
    }

    /**
     * Adds the a Google Authenticator Device.
     *
     * @param $updateToken
     * @param string $sessionIdToIgnore
     * @param $code
     * @return string
     */
    public function addGoogleAuthenticator($updateToken, $code, $sessionIdToIgnore = NULL) {
        return $this->api->dataPost("user/two_factor/google", [
            'token' => $updateToken,
            'code' => $code,
            'session_id_to_ignore' => $sessionIdToIgnore
        ]);
    }

    /**
     * Deletes the current Google Authenticator Device
     *
     * @param $updateToken
     * @return string
     */
    public function deleteGoogleAuthenticator($updateToken) {
        return $this->api->dataDelete("user/two_factor/google", [
            'token' => $updateToken
        ]);
    }

    /**
     * Current U2F Information
     * Also contains Information to add new U2F Token.
     *
     * @param $updateToken
     * @return array
     */
    public function getU2F($updateToken) {
        return $this->api->dataGet("user/two_factor/u2f", null, [
            'query' => [
                'token' => $updateToken
            ]
        ])['u2f'];
    }

    /**
     * Adds a new U2F Token
     *
     * @param $updateToken
     * @param $response
     * @param string $sessionIdToIgnore add a session id that will not be terminated
     * @return string
     */
    public function addU2F($updateToken, $response, $sessionIdToIgnore = NULL) {
        return $this->api->dataPost("user/two_factor/u2f", [
            'token' => $updateToken,
            'response' => $response,
            'session_id_to_ignore' => $sessionIdToIgnore
        ]);
    }

    /**
     * Deletes a specific U2F Token
     *
     * @param $updateToken
     * @param $key
     * @return string
     */
    public function deleteU2F($updateToken, $key) {
        return $this->api->dataDelete("user/two_factor/u2f", [
            'token' => $updateToken,
            'key' => $key
        ]);
    }

}
