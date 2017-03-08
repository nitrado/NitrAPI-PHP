<?php

namespace Nitrapi\Customer;

use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Nitrapi;

class Customer
{

    private $api;
    private $data;

    public function __construct(Nitrapi $api)
    {
        $this->api = $api;
        $this->data = $api->dataGet("user")['user'];
    }

    /**
     * Returns the User ID.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->data['user_id'];
    }

    /**
     * Returns the Username.
     *
     * @return string username
     */
    public function getUsername()
    {
        return $this->data['username'];
    }

    /**
     * Returns true if the User is already activated.
     *
     * @return bool
     */
    public function isActivated()
    {
        return $this->data['activated'];
    }

    /**
     * Return the User Timezone.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->data['timezone'];
    }

    /**
     * Updates the User Timezone.
     *
     * @param $updateToken
     * @param $newTimezone
     * @return bool
     */
    public function setTimezone($updateToken, $newTimezone)
    {
        $this->api->dataPost('user', [
            'timezone' => $newTimezone,
            'token' => $updateToken
        ]);
        $this->data['timezone'] = $newTimezone;
        return true;
    }

    /**
     * Returns email address of the user.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->data['email'];
    }

    /**
     * Returns credit.
     *
     * @return int
     */
    public function getCredit()
    {
        return $this->data['credit'];
    }

    /**
     * Returns the currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->data['currency'];
    }

    /**
     * Return the Registration Date.
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return (new \DateTime())->setTimestamp(strtotime($this->data['registered']));
    }

    /**
     * Return the User language as 3 digit ISO code.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->data['language'];
    }

    /**
     * Returns the Avatar URL if available.
     *
     * @return string|null
     */
    public function getAvatar()
    {
        return $this->data['avatar'];
    }

    /**
     * Returns true if Donations are enabled.
     *
     * @return bool
     */
    public function hasDonationsEnabled()
    {
        return $this->data['donations'];
    }

    /**
     * Returns the User Phone censored Number and Status
     *
     * @return array
     */
    public function getPhone() {
        return $this->data['phone'];
    }

    /**
     * Returns a array with the enabled Two Factor Methods.
     *
     * @return array
     */
    public function getTwoFactorMethods() {
        return $this->data['two_factor'];
    }

    /**
     * @deprecated
     */
    public function getPersonalData()
    {
        return $this->getProfile();
    }

    /**
     * Returns the Customer profile information.
     *
     * @return array
     */
    public function getProfile() {
        return $this->data['profile'];
    }

    /**
     * Update the Customer profile information.
     *
     * @param $updateToken
     * @param array $profile
     * @return bool
     */
    public function updateProfile($updateToken, array $profile) {
        $data = [];
        $data['token'] = $updateToken;
        $data['profile'] = $profile;

        $this->api->dataPost('user', $data);
        return true;
    }

    /**
     * Returns a webinterface token
     *
     * @return string
     */
    public function getWebinterfaceToken()
    {
        $token = $this->api->dataGet('user/webinterface_token');
        return $token['token']['token'];
    }

    /**
     * Deletes all webinterface tokens. This logs out all users from your webinterface.
     *
     * @return string
     */
    public function deleteWebinterfaceTokens()
    {
        try {
            $this->api->dataDelete('user/webinterface_token');
        } catch (NitrapiHttpErrorException $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns a update token for this user.
     *
     * @param $password
     * @return array|bool
     */
    public function getUpdateToken($password)
    {
        return $this->api->dataPost('user/token', [
            'password' => $password
        ]);
    }

    /**
     * Updates the user password.
     *
     * @param $updateToken
     * @param $newPassword
     * @return bool
     */
    public function changePassword($updateToken, $newPassword)
    {
        $this->api->dataPost('user', [
            'password' => $newPassword,
            'token' => $updateToken
        ]);
        return true;
    }

    /**
     * Updates the user donation setting.
     *
     * @param $updateToken
     * @param $newState
     * @return bool
     */
    public function setDonations($updateToken, $newState = true)
    {
        $this->api->dataPost('user', [
            'donations' => (($newState) ? 'true' : 'false'),
            'token' => $updateToken
        ]);
        $this->data['donations'] = $newState;
        return true;
    }

}