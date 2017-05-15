<?php

namespace Nitrapi\Common\Exceptions;

class NitrapiException extends \Exception
{
    protected $errorId = null;

    public function setErrorId($errorId) {
        $this->errorId = $errorId;
    }

    public function getErrorId() {
        $this->errorId;
    }
}