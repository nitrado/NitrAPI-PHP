<?php

namespace Nitrapi\Common\Exceptions;

use Psr\Http\Message\ResponseInterface;

class NitrapiException extends \Exception
{
    protected $errorId = null;

    /**
     * @var null|ResponseInterface
     */
    protected $response = null;

    public function getErrorId() {
        return $this->errorId;
    }

    public function setErrorId($errorId) {
        $this->errorId = $errorId;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @param ResponseInterface|null $response
     */
    public function setResponse($response) {
        $this->response = $response;
    }
}
