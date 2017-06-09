<?php

namespace Nitrapi\Customer;

class AccessToken {
    private $token;
    private $expiresAt;
    private $tokenType;
    private $scopes;
    private $refreshToken;

    public function __construct($data) {
        $this->token = $data['access_token'];

        $this->expiresAt = !empty($data['expires_at']) ? $data['expires_at'] : ($data['expires_in'] + time());
        $this->tokenType = $data['token_type'];
        $this->scopes = explode(' ', $data['scope']);

        if (!empty($data['refresh_token'])) {
            $this->refreshToken = $data['refresh_token'];
        }
    }

    public function getToken() {
        return $this->token;
    }

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function getExpiresAt() {
        return $this->expiresAt;
    }

    public function getExpiresIn() {
        return $this->getExpiresAt() - time();
    }

    public function getType() {
        return $this->tokenType;
    }

    public function getScopes() {
        return $this->scopes;
    }

    public function hasScope($scope) {
        return in_array($scope, $this->getScopes());
    }
}