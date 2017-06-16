<?php

namespace Nitrapi\Admin;

use Nitrapi\Nitrapi;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Customer\AccessToken;
use Nitrapi\Admin\Servers\ServerManager;

class Admin extends NitrapiObject
{

    /**
     * Returns the ServerManager
     *
     * @return ServerManager
     */
    public function getServerManager() {
        return new ServerManager($this);
    }

    /**
     * Requests a sub token for another user. This is required for calls that access the user's services.
     *
     * @param $userId The user to generate a token for
     * @param array|string $scopes The scopes for the new token. Can only contain scopes of the current token.
     * May be passed in as array or space separated string.
     * @param null|integer $serviceId A serviceID to pin the token to. It won't be possible to access other services with this
     * token. Passing null allows all services to be accessed.
     * @param null|integer $expires_in The time in seconds the new token will be valid for. The life time of a sub token
     * can never exceed the life time of the parent token. Pass in null for taking the parent token's life time.
     *
     * @return AccessToken
     */
    public function getUserToken($userId, $scopes, $serviceId = null, $expires_in = null) {
        $scopeString = $scopes;
        if (is_array($scopes)) {
            $scopeString = implode(' ', $scopes);
        }

        $payload = [
            'scope' => $scopeString,
            'user_id' => $userId
        ];

        if (!empty($serviceId)) {
            $payload['service_id'] = $serviceId;
        }

        if (!empty($expires_in)) {
            $payload['expires_in'] = $expires_in;
        }

        return new AccessToken($this->getApi()->dataPost('token/sub', $payload)['token']);
    }
}