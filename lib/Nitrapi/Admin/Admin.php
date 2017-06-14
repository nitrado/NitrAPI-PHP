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

    public function getUserToken($userId, $scopes, $serviceId = null) {
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

        return new AccessToken($this->getApi()->dataPost('token/sub', $payload)['token']);
    }
}