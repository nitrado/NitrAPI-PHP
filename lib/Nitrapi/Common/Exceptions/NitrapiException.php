<?php

namespace Nitrapi\Common\Exceptions;

use Psr\Http\Message\ResponseInterface;

class NitrapiException extends \Exception
{
    protected $errorId;

    /**
     * @var null|ResponseInterface
     */
    protected $response;

    public function getErrorId()
    {
        return $this->errorId;
    }

    public function setErrorId($errorId): void
    {
        $this->errorId = $errorId;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface|null $response
     */
    public function setResponse(?ResponseInterface $response): void
    {
        $this->response = $response;
    }
}
