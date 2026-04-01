<?php

namespace Nitrapi\Services\Voiceservers\Types;

use Nitrapi\Common\Exceptions\NitrapiException;

class Mumble extends Type
{
    /**
     * Adds a new User
     *
     * @param string $username
     * @param string $password
     * @return true
     * @throws NitrapiException
     */
    public function addUser(string $username, string $password): bool
    {
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
     * @return true
     * @throws NitrapiException
     */
    public function deleteUser(string $username): bool
    {
        $url = "services/" . $this->service->getId() . "/voiceservers/mumble/user";
        $this->service->getApi()->dataDelete($url, [
            'username' => $username,
        ]);

        return true;
    }
}
