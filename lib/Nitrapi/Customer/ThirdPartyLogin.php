<?php

namespace Nitrapi\Customer;

use Nitrapi\Nitrapi;

class ThirdPartyLogin
{

    private $api;

    public function __construct(Nitrapi $api)
    {
        $this->api = $api;
    }

    /**
     * List all connected 3rd Party accounts.
     *
     * @return mixed
     */
    public function getList() {
        return $this->api->dataGet("/user/third_party")['connections'];
    }

    /**
     * Add a Google Account.
     * This method returns a URL, you need to redirect the User to this URL.
     *
     * @param $updateToken
     * @param $redirectUrl
     * @return string
     */
    public function addGoogle($updateToken, $redirectUrl) {
        return $this->api->dataPost("/user/third_party/google", [
            'token' => $updateToken,
            'redirect_url' => $redirectUrl,
        ])['url'];
    }

    /**
     * Add a Facebook Account.
     * This method returns a URL, you need to redirect the User to this URL.
     *
     * @param $updateToken
     * @param $redirectUrl
     * @return string
     */
    public function addFacebook($updateToken, $redirectUrl) {
        return $this->api->dataPost("/user/third_party/facebook", [
            'token' => $updateToken,
            'redirect_url' => $redirectUrl,
        ])['url'];
    }

    /**
     * Add a Twitch Account.
     * This method returns a URL, you need to redirect the User to this URL.
     *
     * @param $updateToken
     * @param $redirectUrl
     * @return string
     */
    public function addTwitch($updateToken, $redirectUrl) {
        return $this->api->dataPost("/user/third_party/twitch", [
            'token' => $updateToken,
            'redirect_url' => $redirectUrl,
        ])['url'];
    }

    /**
     * Add a Github Account.
     * This method returns a URL, you need to redirect the User to this URL.
     *
     * @param $updateToken
     * @param $redirectUrl
     * @return string
     */
    public function addGithub($updateToken, $redirectUrl) {
        return $this->api->dataPost("/user/third_party/github", [
            'token' => $updateToken,
            'redirect_url' => $redirectUrl,
        ])['url'];
    }

    /**
     * Add a Microsoft Account.
     * This method returns a URL, you need to redirect the User to this URL.
     *
     * @param $updateToken
     * @param $redirectUrl
     * @return string
     */
    public function addMicrosoft($updateToken, $redirectUrl) {
        return $this->api->dataPost("/user/third_party/microsoft", [
            'token' => $updateToken,
            'redirect_url' => $redirectUrl,
        ])['url'];
    }

    /**
     * Deletes a existing Google connection.
     *
     * @param $updateToken
     * @return bool
     */
    public function deleteGoogle($updateToken) {
        return $this->api->dataDelete("/user/third_party/google", [
            'token' => $updateToken
        ]);
    }

    /**
     * Deletes a existing Facebook connection.
     *
     * @param $updateToken
     * @return bool
     */
    public function deleteFacebook($updateToken) {
        return $this->api->dataDelete("/user/third_party/facebook", [
            'token' => $updateToken
        ]);
    }

    /**
     * Deletes a existing Twitch connection.
     *
     * @param $updateToken
     * @return bool
     */
    public function deleteTwitch($updateToken) {
        return $this->api->dataDelete("/user/third_party/twitch", [
            'token' => $updateToken
        ]);
    }

    /**
     * Deletes a existing Github connection.
     *
     * @param $updateToken
     * @return bool
     */
    public function deleteGithub($updateToken) {
        return $this->api->dataDelete("/user/third_party/github", [
            'token' => $updateToken
        ]);
    }

    /**
     * Deletes a existing Microsoft connection.
     *
     * @param $updateToken
     * @return bool
     */
    public function deleteMicrosoft($updateToken) {
        return $this->api->dataDelete("/user/third_party/microsoft", [
            'token' => $updateToken
        ]);
    }

}