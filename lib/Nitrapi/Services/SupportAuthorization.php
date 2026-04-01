<?php

namespace Nitrapi\Services;

use DateTime;
use Nitrapi\Nitrapi;
use Nitrapi\Common\NitrapiObject;

class SupportAuthorization extends NitrapiObject
{
    protected $data;

    public function __construct(Nitrapi $api, $data)
    {
        parent::__construct($api);

        $this->data = $data;
    }

    public function getExpiresAt(): DateTime
    {
        return (new DateTime())->setTimestamp(strtotime($this->data['support_authorization']['expires_at']));
    }

    public function getCreatedAt(): DateTime
    {
        return (new DateTime())->setTimestamp(strtotime($this->data['support_authorization']['created_at']));
    }
}
