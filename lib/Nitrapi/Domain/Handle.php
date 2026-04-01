<?php

namespace Nitrapi\Domain;

use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class Handle extends NitrapiObject
{
    /**
     * @var $api Nitrapi
     */
    protected $api;

    /**
     * @var $data array
     */
    protected $data;

    public function __construct(Nitrapi $api, array $data = [])
    {
        parent::__construct($api);
        $this->setData($data);
    }

    public function setData($data): self
    {
        if (count($data) > 0) {
            $this->data = $data;
        }

        return $this;
    }

    public function getHandle()
    {
        if (!empty($this->data['handle'])) {
            return $this->data['handle'];
        }

        return null;
    }

    /**
     * @throws NitrapiException
     */
    public function getDomainList()
    {
        return $this->getApi()->dataGet('/domain/contact/' . $this->getHandle() . '/domains')['domains'];
    }

    public function setOrganization($organization)
    {
        return $this->data['organization'] = $organization;
    }

    public function getOrganization()
    {
        if (!empty($this->data['organization'])) {
            return $this->data['organization'];
        }

        return null;
    }

    public function setFirstName($firstName): self
    {
        $this->data['first_name'] = $firstName;
        return $this;
    }

    public function getFirstName()
    {
        return $this->data['first_name'];
    }

    public function setLastName($lastName): self
    {
        $this->data['last_name'] = $lastName;
        return $this;
    }

    public function getLastName()
    {
        return $this->data['last_name'];
    }

    public function setEMail($email): self
    {
        $this->data['email'] = $email;
        return $this;
    }

    public function getEMail()
    {
        return $this->data['email'];
    }

    public function setPhone($phone): self
    {
        $this->data['phone'] = $phone;
        return $this;
    }

    public function getPhone()
    {
        return $this->data['phone'];
    }

    public function setFax($fax): self
    {
        $this->data['fax'] = $fax;
        return $this;
    }

    public function getFax()
    {
        if (!empty($this->data['fax'])) {
            return $this->data['fax'];
        }

        return null;
    }

    public function setStreet($street): self
    {
        $this->data['street'] = $street;
        return $this;
    }

    public function getStreet()
    {
        return $this->data['street'];
    }

    public function setPostCode($postCode): self
    {
        $this->data['postcode'] = $postCode;
        return $this;
    }

    public function getPostCode()
    {
        return $this->data['postcode'];
    }

    public function setCity($city): self
    {
        $this->data['city'] = $city;
        return $this;
    }

    public function getCity()
    {
        return $this->data['city'];
    }

    public function setState($state): self
    {
        $this->data['state'] = $state;
        return $this;
    }

    public function getState()
    {
        return $this->data['state'];
    }

    public function setCountry($country): self
    {
        $this->data['country'] = $country;
        return $this;
    }

    public function getCountry()
    {
        return $this->data['country'];
    }

    /**
     * @throws NitrapiException
     */
    public function save()
    {
        $request = [
            'contact' => [
                'organization' => $this->getOrganization(),
                'first_name' => $this->getFirstName(),
                'last_name' => $this->getLastName(),
                'email' => $this->getEMail(),
                'phone' => $this->getPhone(),
                'fax' => $this->getFax(),
                'street' => $this->getStreet(),
                'postcode' => $this->getPostCode(),
                'city' => $this->getCity(),
                'state' => $this->getState(),
                'country' => $this->getCountry(),
            ],
        ];

        // New handle
        if ($this->getHandle() === null) {
            return $this->getApi()->dataPost('/domain/contact', $request);
        }

        //Update
        return $this->getApi()->dataPut('/domain/contact/' . $this->getHandle(), $request);
    }

    /**
     * @throws NitrapiException
     */
    public function delete()
    {
        return $this->getApi()->dataDelete('/domain/contact/' . $this->getHandle());
    }

}
