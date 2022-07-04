<?php
namespace Nitrapi\Customer;

use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Nitrapi;

class Registration {

    private $api;
    private $data;

    public function __construct(Nitrapi $api,
                                $oAuthClientId,
                                $oAuthClientSecret,
                                $userName,
                                $email,
                                $password,
                                $consentPrivacy,
                                $consentAge,
                                $consentTos,
                                $recaptchaResponse = null,
                                $currency = null,
                                $language = null,
                                $timezone = null,
                                $consentNewsletter = false) {
        $this->api = $api;
        $parameters = [
            "client_id" => $oAuthClientId,
            "client_secret" => $oAuthClientSecret,
            "username" => $userName,
            "email" => $email,
            "password" => $password,
            "consent_privacy" => $consentPrivacy,
            "consent_age" => $consentAge,
            "consent_tos" => $consentTos,
            "consent_newsletter" => $consentNewsletter
        ];

        if (!empty($recaptchaResponse)) {
            $parameters["recaptcha"] = $recaptchaResponse;
        }

        if (!empty($currency)) {
            $parameters["currency"] = $currency;
        }

        if (!empty($language)) {
            $parameters["language"] = $language;
        }

        if (!empty($timezone)) {
            $parameters["timezone"] = $timezone;
        }

        $this->data = $this->api->dataPost("registration", $parameters);
    }

    public static function getRecaptchaSiteKey(Nitrapi $api) {
        $data = $api->dataGet("registration");
        if (empty($data["registration"]["google_recaptcha"]["enabled"])) {
            return false;
        }

        return $data["registration"]["google_recaptcha"]["key"];
    }

    public function getUserId() {
        return $this->data["registration"]["user"]["id"];
    }

    public function getAccessToken() {
        return $this->data["registration"]["oauth"]["access_token"];
    }

    public function getRefreshToken() {
        return $this->data["registration"]["oauth"]["refresh_token"];
    }

    public function getTokenExpiration() {
        return $this->data["registration"]["oauth"]["expires_in"];
    }

    public function getTokenScope() {
        return $this->data["registration"]["oauth"]["scope"];
    }
}
