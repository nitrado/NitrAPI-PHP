<?php

namespace Nitrapi\Services\Bouncers;


class BouncerDetails
{
    protected $data;

    public function __construct(array &$data)
    {
        $this->data = $data;
    }

    public function getIdents(): array
    {
        return array_map(static function ($i) {
            return new Ident($i);
        }, $this->data['bouncers']);
    }

    public function getType(): string
    {
        return (string)$this->data['type'];
    }

    public function getIdentLimit(): int
    {
        return (int)$this->data['max_bouncer'];
    }
}
