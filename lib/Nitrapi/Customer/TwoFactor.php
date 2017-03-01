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

    public function getGoogleAuthenticator($updateToken) {
        return $this->api->dataGet("user/two_factor/google", null, [
            'query' => [
                'token' => $updateToken
            ]
        ]);
    }

    public function addGoogleAuthenticator($updateToken, $code) {
        return $this->api->dataPost("user/two_factor/google", [
            'token' => $updateToken,
            'code' => $code
        ]);
    }


    public function deleteGoogleAuthenticator($updateToken) {
        return $this->api->dataDelete("user/two_factor/google", [
            'token' => $updateToken
        ]);
    }

}