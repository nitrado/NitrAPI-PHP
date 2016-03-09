<?php

namespace Nitrapi\Services\Voiceservers\Types;

class Mumble extends Type
{
    /**
     * Adds a new User
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    public function addUser($username, $password) {
        $url = "services/" . $this->service->getId() . "/voiceservers/mumble/user";
        $this->service->getApi()->dataPost($url, [
            'username' => $username,
            'password' => $password,
        ]);

        return true;

    }

    /**
     * Deletes a User
     *
     * @param string $username
     * @return string
     */
    public function deleteUser($username) {
        $url = "services/" . $this->service->getId() . "/voiceservers/mumble/user";
        $this->service->getApi()->dataDelete($url, [
            'username' => $username
        ]);

        return true;
    }
}