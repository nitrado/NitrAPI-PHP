<?php

namespace Nitrapi\SSHKeys;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Nitrapi;

class SSHKey
{
    /**
     * @var Nitrapi
     */
    private $api;

    /**
     * @var array
     */
    private $data;

    public function __construct(Nitrapi $api, array $data)
    {
        $this->api = $api;
        $this->data = $data;
        $this->data['full_public_key'] = $this->data['type'] . ' ' . $this->data['public_key'] . ' ' . $this->data['comment'];
    }

    /**
     * Return the SSH Key ID
     *
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->data['id'];
    }

    /**
     * Return the SSH Key type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->data['type'];
    }

    /**
     * Returns the SSH Key Comment
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->data['comment'];
    }

    /**
     * Returns the full SSH public key
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->data['full_public_key'];
    }

    /**
     * Updates the existing SSH public key
     *
     * @throws NitrapiException
     */
    public function setPublicKey($key): self
    {
        $this->data['full_public_key'] = $key;
        $this->doUpdate();
        return $this;
    }

    /**
     * Returns true if the key is enabled
     */
    public function isEnabled(): bool
    {
        return (bool)$this->data['enabled'];
    }

    /**
     * Returns true if the key is enabled
     *
     * @throws NitrapiException
     */
    public function setEnabled($enabled = true): self
    {
        $this->data['enabled'] = $enabled;
        $this->doUpdate();
        return $this;
    }

    /**
     * Deletes this SSH public key
     *
     * @throws NitrapiException
     */
    public function doDelete(): bool
    {
        $url = "user/ssh_keys/" . $this->data['id'];
        $this->api->dataDelete($url);

        return true;
    }

    /**
     * Updates this SSH public key in database
     *
     * @throws NitrapiException
     */
    private function doUpdate(): void
    {
        $url = "user/ssh_keys/" . $this->data['id'];
        $this->api->dataPost($url, [
            'key' => $this->getPublicKey(),
            'enabled' => ($this->isEnabled() ? 'true' : 'false'),
        ]);
    }
}
