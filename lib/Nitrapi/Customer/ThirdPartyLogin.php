<?php

namespace Nitrapi\Customer;

use Nitrapi\Common\Exceptions\NitrapiException;
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
     * @throws NitrapiException
     */
    public function getList()
    {
        return $this->api->dataGet("/user/third_party")['connections'];
    }

    /**
     * Add a Google Account.
     * This method returns a URL, you need to redirect the User to this URL.
     *
     * @param $updateToken
     * @param $redirectUrl
     * @return string
     * @throws NitrapiException
     */
    public function addGoogle($updateToken, $redirectUrl): string
    {
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
     * @throws NitrapiException
     */
    public function addFacebook($updateToken, $redirectUrl): string
    {
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
     * @throws NitrapiException
     */
    public function addTwitch($updateToken, $redirectUrl): string
    {
        return $this->api->dataPost("/user/third_party/twitch", [
            'token' => $updateToken,
            'redirect_url' => $redirectUrl,
        ])['url'];
    }

    /**
     * Add a GitHub Account.
     * This method returns a URL, you need to redirect the User to this URL.
     *
     * @param $updateToken
     * @param $redirectUrl
     * @return string
     * @throws NitrapiException
     */
    public function addGithub($updateToken, $redirectUrl): string
    {
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
     * @throws NitrapiException
     */
    public function addMicrosoft($updateToken, $redirectUrl): string
    {
        return $this->api->dataPost("/user/third_party/microsoft", [
            'token' => $updateToken,
            'redirect_url' => $redirectUrl,
        ])['url'];
    }

    /**
     * Deletes an existing Google connection.
     *
     * @param $updateToken
     * @return bool
     * @throws NitrapiException
     */
    public function deleteGoogle($updateToken): bool
    {
        return $this->api->dataDelete("/user/third_party/google", [
            'token' => $updateToken,
        ]);
    }

    /**
     * Deletes an existing Facebook connection.
     *
     * @param $updateToken
     * @return bool
     * @throws NitrapiException
     */
    public function deleteFacebook($updateToken): bool
    {
        return $this->api->dataDelete("/user/third_party/facebook", [
            'token' => $updateToken,
        ]);
    }

    /**
     * Deletes an existing Twitch connection.
     *
     * @param $updateToken
     * @return bool
     * @throws NitrapiException
     */
    public function deleteTwitch($updateToken): bool
    {
        return $this->api->dataDelete("/user/third_party/twitch", [
            'token' => $updateToken,
        ]);
    }

    /**
     * Deletes an existing GitHub connection.
     *
     * @param $updateToken
     * @return bool
     * @throws NitrapiException
     */
    public function deleteGithub($updateToken): bool
    {
        return $this->api->dataDelete("/user/third_party/github", [
            'token' => $updateToken,
        ]);
    }

    /**
     * Deletes an existing Microsoft connection.
     *
     * @param $updateToken
     * @return bool
     * @throws NitrapiException
     */
    public function deleteMicrosoft($updateToken): bool
    {
        return $this->api->dataDelete("/user/third_party/microsoft", [
            'token' => $updateToken,
        ]);
    }
}
