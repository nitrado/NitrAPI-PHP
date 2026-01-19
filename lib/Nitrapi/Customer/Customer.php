<?php

namespace Nitrapi\Customer;

use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class Customer extends NitrapiObject
{

    private $data;

    public function __construct(Nitrapi $api)
    {
        parent::__construct($api);
        $this->data = $this->getApi()->dataGet("user")['user'];
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
        $this->getApi()->dataPost('user', [
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
     * Returns a array with the enabled Two Factor Methods.
     *
     * @return array
     */
    public function getTwoFactorMethods() {
        return $this->data['two_factor'];
    }

    /**
     * A user can have various permissions associated to it. This can be used to
     * limit functionality on the UI or add additional features if the user has
     * a certain permission. The permission reflect also the capability of the
     * API endpoints. If the user do not have the permission to order domains, the
     * order domains endpoint results in an error. The list of all possible
     * permissions can be changed over time, so there exist no final list of them.
     *
     * @return array The permissions associated to the user
     */
    public function getPermissions() {
        return $this->data['permissions'];
    }

    /**
     * Check if the user has the given permission. The permission is a string
     * (all uppercase).
     *
     * @see Customer::getPermissions()
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission) {
        return in_array($permission, $this->getPermissions(), true);
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

        $this->getApi()->dataPost('user', $data);
        return true;
    }

    /**
     * Returns an update token for this user.
     *
     * @param $password
     * @return array|bool
     */
    public function getUpdateToken($password)
    {
        return $this->getApi()->dataPost('user/token', [
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
        $this->getApi()->dataPost('user', [
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
        $this->getApi()->dataPost('user', [
            'donations' => (($newState) ? 'true' : 'false'),
            'token' => $updateToken
        ]);
        $this->data['donations'] = $newState;
        return true;
    }

    /**
     * Requests a sub token for this user. This can be used to drop scopes on the token.
     *
     * @param array|string $scopes The scopes for the new token. Can only contain scopes of the current token.
     * May be passed in as array or space separated string.
     * @param null|integer $serviceId A serviceID to pin the token to. It won't be possible to access other services with this
     * token. Passing null allows all services to be accessed.
     * @param null|integer $expires_in The time in seconds the new token will be valid for. The life time of a sub token
     * can never exceed the life time of the parent token. Pass in null for taking the parent token's life time.
     *
     * @return AccessToken
     */
    public function getSubToken($scopes, $serviceId = null, $expires_in = null) {
        $scopeString = $scopes;
        if (is_array($scopes)) {
            $scopeString = implode(' ', $scopes);
        }

        $payload = [
            'scope' => $scopeString
        ];

        if (!empty($serviceId)) {
            $payload['service_id'] = $serviceId;
        }

        if (!empty($expires_in)) {
            $payload['expires_in'] = $expires_in;
        }

        return new AccessToken($this->getApi()->dataPost('token/sub', $payload)['token']);
    }

    /**
     * Return the User's rate limit for the Nitrado API
     *
     * @return int
     */
    public function getRateLimit()
    {
        return isset($this->data['rate_limit']) ? $this->data['rate_limit'] : 15000;
    }

    /**
     * Get the subscribed and active newsletter campaigns
     *
     * @return array
     */
    public function getNewsletterCampaigns()
    {
        if(isset($this->data['newsletter_campaigns'])){
            return $this->data['newsletter_campaigns'];
        }
        return [];
    }

    /**
     * Subscribe to newsletter campaign
     *
     * @param int $newsletterCampaignId
     * @return true
     */
    public function subscribeToNewsletterCampaign($newsletterCampaignId)
    {
        $this->getApi()->dataPost('user/newsletter_campaign/'.$newsletterCampaignId.'/subscribe');
        return true;
    }

    /**
     * Unsubscribe from newsletter campaign
     *
     * @param int $newsletterCampaignId
     * @return true
     */
    public function unsubscribeFromNewsletterCampaign($newsletterCampaignId)
    {
        $this->getApi()->dataPut('user/newsletter_campaign/'.$newsletterCampaignId.'/unsubscribe');
        return true;
    }
}
