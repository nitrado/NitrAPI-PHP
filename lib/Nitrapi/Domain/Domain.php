<?php

namespace Nitrapi\Domain;

use DateTime;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class Domain extends NitrapiObject
{
    public const AUTH_CODE_STATUS_AVAILABLE = 'available';
    public const AUTH_CODE_STATUS_NOT_REQUESTED = 'not_requested';
    public const AUTH_CODE_STATUS_PENDING = 'pending';

    /**
     * @var $data array
     */
    protected $data;

    public function __construct(Nitrapi $api, array $data = [])
    {
        parent::__construct($api);
        $this->setData($data);
    }

    /**
     * Sets data field $data to specified value.
     *
     * @param $data
     * @return $this
     */
    public function setData($data): self
    {
        if (count($data) > 0) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Returns the domain id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->data['id'];
    }

    /**
     * Returns status of domain
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->data['status'];
    }

    /**
     * Returns DateTime of domain deletion
     *
     * @return DateTime|null
     */
    public function getDeleteAt(): ?DateTime
    {
        if (empty($this->data['delete_at'])) {
            return null;
        }

        return (new DateTime())->setTimestamp(strtotime($this->data['delete_at']));
    }

    /**
     * Returns the shorthand of the registrar
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->data['provider'];
    }

    /**
     * Returns the cancelperiod in days
     *
     * @return int days
     */
    public function getCancelperiod(): int
    {
        return (int)$this->data['cancelperiod'];
    }

    /**
     * Returns the duration of 1 runtime in seconds
     *
     * @return int seconds
     */
    public function getDuration(): int
    {
        return (int)$this->data['duration'];
    }

    /**
     * Returns integer whether domain will be deleted when it expires
     *
     * @return int
     */
    public function deleteOnExpire(): int
    {
        return $this->data['delete_on_expire'];
    }

    public function setDomain(string $domain): self
    {
        $this->data['domain'] = $domain;

        return $this;
    }

    /**
     * Returns the FQDN
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->data['domain'];
    }

    /**
     * Returns the date until the domain is able to renew.
     *
     * @return DateTime
     */
    public function getRenewUntil(): DateTime
    {
        return (new DateTime())->setTimestamp(strtotime($this->data['renew_until']));
    }

    /**
     * Returns the date until the domain has been paid.
     *
     * @return DateTime
     */
    public function getPaidUntil(): DateTime
    {
        return (new DateTime())->setTimestamp(strtotime($this->data['paid_until']));
    }

    /**
     * Sets new nameserver for the domain.
     * If no nameserver has been set, the default settings will be restored.
     *
     * @param array<int, string>|null $nameserver
     * @return array|bool|string
     * @throws NitrapiException
     */
    public function setNameserver(?array $nameserver = null)
    {
        $data = [];

        if (!empty($nameserver)) {
            foreach ($nameserver as $key => $ns) {
                $ns_id = $key + 1;
                $data['nameserver' . $ns_id] = $ns;
            }
        }

        $result = $this->getApi()->dataPut('/domain/' . $this->getDomain() . '/nameserver', $data);
        $this->data['nameserver'] = $nameserver;
        return $result;
    }

    /**
     * Returns the current nameserver list.
     *
     * @return array
     */
    public function getNameserver(): array
    {
        return $this->data['nameserver'];
    }

    /**
     * Returns an array of DNS Records
     *
     * @return Record[]
     * @throws NitrapiException
     */
    public function getDNSRecords(): array
    {
        $records = [];
        $nitrapi = $this->getApi();

        foreach ($this->getApi()->dataGet("/domain/" . $this->getDomain() . "/records") as $record) {
            $records[] = new Record($nitrapi, $this->getDomain(), $record);
        }
        return $records;
    }

    /**
     * Inserts a new DNS record
     *
     * @param string $name
     * @param string $type
     * @param string $content
     * @param int $ttl
     * @return array|bool|string
     * @throws NitrapiException
     */
    public function setDNSRecord(string $name, string $type, string $content, int $ttl)
    {
        $data = [
            "name" => $name,
            "type" => $type,
            "content" => $content,
            "ttl" => $ttl,
        ];
        return $this->getApi()->dataPost("/domain/" . $this->getDomain() . "/records", $data);
    }

    /**
     * Returns an array with all available DNS record types and their displayed name
     *
     * @return array
     * @throws NitrapiException
     */
    public function getRecordTypes(): array
    {
        return $this->getApi()->dataGet("/domain/" . $this->getDomain() . "/record_types");
    }

    /**
     * Returns an array with all available redirect types and their displayed name
     *
     * @return array
     * @throws NitrapiException
     */
    public function getRedirectTypes(): array
    {
        return $this->getApi()->dataGet("/domain/" . $this->getDomain() . "/redirect_types");
    }

    /**
     * Returns all redirects
     *
     * @return Redirect[]
     * @throws NitrapiException
     */
    public function getRedirects(): array
    {
        $nitrapi = $this->getApi();
        $redirects = [];
        foreach ($this->getApi()->dataGet("/domain/" . $this->getDomain() . "/redirects") as $redirect) {
            $redirects[] = new Redirect($nitrapi, $this->getDomain(), $redirect);
        }
        return $redirects;
    }

    /**
     * Insert a redirect
     *
     * @param string $sld
     * @param string $type
     * @param string $target
     * @param string $pagetitle
     * @param string $metadescr
     * @param string $metakey
     * @return string
     * @throws NitrapiException
     */
    public function setRedirect(
        string $sld,
        string $type,
        string $target,
        string $pagetitle = "",
        string $metadescr = "",
        string $metakey = ""
    ): string {
        $data = [
            "subdomain" => $sld,
            "type" => $type,
            "target" => $target,
            "pagetitle" => $pagetitle,
            "metadescription" => $metadescr,
            "metakey" => $metakey,
        ];

        return $this->getApi()->dataPost("/domain/" . $this->getDomain() . "/redirects", $data);
    }

    /**
     * Returns settings of the DNS zone
     *
     * @return Zone
     * @throws NitrapiException
     */
    public function getZone(): Zone
    {
        $result = $this->getApi()->dataGet("/domain/" . $this->getDomain() . "/zone");
        $nitrapi = $this->getApi();
        return new Zone($nitrapi, $this->getDomain(), $result);
    }

    /**
     * Returns boolean true if
     *
     * @return bool
     */
    public function hasDns(): bool
    {
        return $this->data['dns'] == 1;
    }

    /**
     * Returns the handle of the domain
     *
     * @return string
     */
    public function getHandle(): string
    {
        return $this->data['handle'];
    }

    /**
     * Extends the Domain instantly.
     *
     * @return array|bool|string
     * @throws NitrapiException
     */
    public function doExtend()
    {
        return $this->getApi()->dataPost('/domain/' . $this->getDomain() . '/extend');
    }

    /**
     * Returns the Auth Code and the Auth Code status.
     *
     * @return array
     * @throws NitrapiException
     */
    public function getAuthCode(): array
    {
        return $this->getApi()->dataGet('/domain/' . $this->getDomain() . '/auth_code');
    }

    /**
     * Requesting the Auth Code.
     * It can take up to 24 hours until the Auth Code is available.
     * Requesting the Auth Code removes the transfer lock of the domain.
     *
     * @return array|bool|string
     * @throws NitrapiException
     */
    public function createAuthCode()
    {
        return $this->getApi()->dataPost('/domain/' . $this->getDomain() . '/auth_code');
    }

    /**
     * Delete the Auth Code.
     * If you delete the Auth Code, the transfer lock will be re-enabled again.
     *
     * @return array
     * @throws NitrapiException
     */
    public function deleteAuthCode(): array
    {
        return $this->getApi()->dataDelete('/domain/' . $this->getDomain() . '/auth_code');
    }

    /**
     * @throws NitrapiException
     */
    public function changeHandle(Handle $handle, $type = HandleManager::TYPE_OWNER_C)
    {
        return $this->getApi()->dataPut('/domain/' . $this->getDomain() . '/handle/' . $handle->getHandle(), [
            'type' => $type,
        ]);
    }

    /**
     * This method returns true if the domain is available to be registered.
     *
     * @return bool
     * @throws NitrapiException
     */
    public function isFree(): bool
    {
        return $this->getApi()->dataGet('/domain/' . $this->getDomain() . '/check')['check']['free'];
    }

    /**
     * Returns the corresponding service id.
     *
     * @return int
     *
     * @deprecated feature no longer existent
     */
    public function getServiceId(): int
    {
        return (int)$this->data['service_id'];
    }

    /**
     * Returns the expire date.
     *
     * @return DateTime
     */
    public function getExpireDate(): DateTime
    {
        return (new DateTime())->setTimestamp(strtotime($this->data['expires']));
    }

    /**
     * Returns the tld ID.
     *
     * @return int
     */
    public function getTldId(): int
    {
        return (int)$this->data['tld_id'];
    }

    /**
     * Returns whether nameserver edits are blocked.
     *
     * @return int
     */
    public function nameserverEditBlocked(): int
    {
        return (int)$this->data['block_nameserver_edit'];
    }

    /**
     * Returns information about the DNS Zone
     *
     * @return array
     * @throws NitrapiException
     */
    public function getZoneInfo(): array
    {
        return $this->getApi()->dataGet("/domain/" . $this->getDomain() . "/zone");
    }

    /**
     * Returns all current notifications
     *
     * @return array
     * @throws NitrapiException
     */
    public function getNotifications(): array
    {
        return $this->getApi()->dataGet("/domain/" . $this->getDomain() . "/notifications");
    }

    /**
     * Returns if the domain is registered
     *
     * @return bool
     */
    public function isRegistered(): bool
    {
        return $this->data['registered'] == 1;
    }

    /**
     * Return the renew count
     *
     * @return int
     */
    public function getRenewCount(): int
    {
        return (int)$this->data['renew_count'];
    }

    /**
     * Return the extend count
     *
     * @return int
     */
    public function getExtendCount(): int
    {
        return (int)$this->data['extend_count'];
    }

    /**
     * Returns information about the domain
     *
     * @return array
     * @throws NitrapiException
     */
    public function getInfo(): array
    {
        return $this->getApi()->dataGet("/domain/" . $this->getDomain() . "/info");
    }

    /**
     * Add domain to a service
     *
     * @param integer $service_id
     * @return string success message
     *
     * @throws NitrapiException
     * @deprecated feature no longer existent
     */
    public function addService(int $service_id): string
    {
        $data = [
            "service_id" => $service_id,
        ];
        return $this->getApi()->dataPut("/domain/" . $this->getDomain() . "/service", $data);
    }

    /**
     * Remove domain from a service
     *
     * @return string
     *
     * @throws NitrapiException
     * @deprecated feature no longer existent
     */
    public function removeService(): string
    {
        return $this->getApi()->dataDelete("/domain/" . $this->getDomain() . "/service");
    }

    /**
     * Returns true if the domain is locked (access restricted
     *
     * @return boolean
     */
    public function isLocked(): bool
    {
        return (bool)$this->data['locked'];
    }
}
