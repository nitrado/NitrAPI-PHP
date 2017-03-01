<?php

namespace Nitrapi\SSHKeys;

use Nitrapi\Nitrapi;

class SSHKey {

    /**
     * @var Nitrapi
     */
    private $api;

    /**
     * @var array
     */
    private $data;

    public function __construct(Nitrapi $api, array $data) {
        $this->api = $api;
        $this->data = $data;
        $this->data['full_public_key'] = $this->data['type'] . ' ' . $this->data['public_key'] . ' ' . $this->data['comment'];
    }

    /**
     * Return the SSH Key ID
     *
     * @return int
     */
    public function getId() {
        return (int)$this->data['id'];
    }

    /**
     * Return the SSH Key type
     *
     * @return string
     */
    public function getType() {
        return $this->data['type'];
    }

    /**
     * Returns the SSH Key Comment
     *
     * @return string
     */
    public function getComment() {
        return $this->data['comment'];
    }

    /**
     * Returns the full SSH public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->data['full_public_key'];
    }

    /**
     * Updates the existing SSH public key
     *
     * @return SSHKey
     */
    public function setPublicKey($key)
    {
        $this->data['full_public_key'] = $key;
        $this->doUpdate();
        return $this;
    }

    /**
     * Returns true if the key is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->data['enabled'];
    }

    /**
     * Returns true if the key is enabled
     *
     * @return SSHKey
     */
    public function setEnabled($enabled = true)
    {
        $this->data['enabled'] = $enabled;
        $this->doUpdate();
        return $this;
    }

    /**
     * Deletes this SSH public key
     *
     * @return bool
     */
    public function doDelete()
    {
        $url = "user/ssh_keys/" . $this->data['id'];
        $this->api->dataDelete($url);

        return true;
    }

    /**
     * Updates this SSH public key in database
     *
     * @return $this
     */
    private function doUpdate()
    {
        $url = "user/ssh_keys/" . $this->data['id'];
        $this->api->dataPost($url, [
            'key' => $this->getPublicKey(),
            'enabled' => ($this->isEnabled() ? 'true' : 'false')
        ]);

        return $this;
    }

}