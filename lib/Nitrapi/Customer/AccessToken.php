<?php

namespace Nitrapi\Customer;

class AccessToken {
    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $tokenType;

    /**
     * @var string[]
     */
    private $scopes;

    /**
     * @var null|string
     */
    private $refreshToken;

    public function __construct($data)
    {
        $this->token = $data['access_token'];

        $this->expiresAt = !empty($data['expires_at']) ? $data['expires_at'] : ($data['expires_in'] + time());
        $this->tokenType = !empty($data['token_type']) ? $data['token_type'] : 'Bearer';
        $this->scopes = $data['scopes'] ?? explode(' ', $data['scope']);

        if (!empty($data['refresh_token'])) {
            $this->refreshToken = $data['refresh_token'];
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    public function getExpiresIn(): int
    {
        return $this->getExpiresAt() - time();
    }

    public function getType(): string
    {
        return $this->tokenType;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function hasScope($scope): bool
    {
        return in_array($scope, $this->getScopes(), true);
    }
}
