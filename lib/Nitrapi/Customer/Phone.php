<?php

namespace Nitrapi\Customer;

use Nitrapi\Nitrapi;

class Phone
{

    private $api;

    public function __construct(Nitrapi $api)
    {
        $this->api = $api;
    }

    /**
     * Adds a mobile phone number to the user profile.
     *
     * Important: Number including country code.
     *
     * @param $updateToken
     * @param $number
     * @return bool
     */
    public function addNumber($updateToken, $number) {
        $this->api->dataPost("user/phone", [
            'token' => $updateToken,
            'number' => $number
        ]);
        return true;
    }

    /**
     * Verifies the new number, code will be sent via SMS.
     *
     * @param $code
     * @return bool
     */
    public function verifyNumber($code) {
        $this->api->dataPost("user/phone/verify", [
            'code' => $code
        ]);
        return true;
    }

    /**
     * Deletes the number.
     *
     * @param $updateToken
     * @return bool
     */
    public function deleteNumber($updateToken) {
        $this->api->dataDelete("user/phone", [
            'token' => $updateToken
        ]);
        return true;
    }

}