<?php

namespace Nitrapi\Common\Http;

use DateTime;
use Nitrapi\Common\Exceptions\NitrapiConcurrencyException;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Common\Exceptions\NitrapiMaintenanceException;
use Nitrapi\Common\Exceptions\NitrapiRateLimitException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client
{
    public const MINIMUM_PHP_VERSION = '7.3.0';

    /** @var ClientInterface */
    protected $httpClient;

    /** @var RequestFactoryInterface */
    protected $requestFactory;

    /** @var StreamFactoryInterface */
    protected $streamFactory;

    /** @var string */
    protected $baseUrl;

    /** @var array */
    protected $defaultQuery = [];

    /** @var string|null */
    protected $accessToken;

    // Rate limit metadata
    /** @var int|false|null */
    protected $rateLimit;
    /** @var int */
    protected $remainingRequests;
    /** @var DateTime */
    protected $rateLimitResetTime;

    /**
     * @throws NitrapiException
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $baseUrl = '',
        ?array $config = null
    ) {
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            throw new NitrapiException(sprintf(
                'You must have PHP version >= %s installed.',
                self::MINIMUM_PHP_VERSION
            ));
        }
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->baseUrl = rtrim($baseUrl, '/');

        if (isset($config['query'])) {
            $this->defaultQuery = $config['query'];
        }
    }

    /**
     * Returns the stream factory (used by FileServer classes for binary uploads/downloads).
     */
    public function getStreamFactory(): StreamFactoryInterface
    {
        return $this->streamFactory;
    }

    /**
     * Set the access token used for Bearer authentication.
     */
    protected function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Returns the current access token.
     */
    protected function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Returns the number of requests allowed per hour.
     *
     * @return int|false|null
     */
    public function getRateLimit()
    {
        return $this->rateLimit;
    }

    /**
     * Returns true if a rate limit is in place.
     */
    public function hasRateLimit(): bool
    {
        return $this->rateLimit !== null && $this->rateLimit !== false;
    }

    /**
     * Returns the number of requests remaining before the rate limit is hit.
     */
    public function getRemainingRequests(): int
    {
        return $this->remainingRequests;
    }

    /**
     * Returns the time at which the rate limit will be reset.
     */
    public function getRateLimitResetTime(): DateTime
    {
        return $this->rateLimitResetTime;
    }

    /**
     * Builds and sends a PSR-7 request. Returns the raw PSR-7 response.
     *
     * Recognised option keys:
     *   headers    array<string,string>  Additional request headers
     *   query      array<string,mixed>   Query-string parameters (merged with defaultQuery)
     *   body       string                Raw request body
     *   form_params array               URL-encoded form body (sets Content-Type automatically)
     *
     * @throws NitrapiHttpErrorException on network-level failures
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        // Resolve URL: prepend base URL when the URL is relative
        if (!preg_match('#^https?://#', $url)) {
            $url = $this->baseUrl . '/' . ltrim($url, '/');
        }

        // Merge and append query string
        $query = array_merge($this->defaultQuery, $options['query'] ?? []);
        if (!empty($query)) {
            $sep = strpos($url, '?') === false ? '?' : '&';
            $url .= $sep . http_build_query($query, '', '&');
        }

        $request = $this->requestFactory->createRequest($method, $url);

        // Authorization header
        if (!empty($this->accessToken)) {
            $request = $request->withHeader('Authorization', 'Bearer ' . $this->accessToken);
        }

        // Additional headers
        foreach ($options['headers'] ?? [] as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        // Body: prefer explicit 'body' string, fall back to url-encoded form_params
        if (isset($options['body'])) {
            $body = is_string($options['body'])
                ? $this->streamFactory->createStream($options['body'])
                : $options['body']; // allow StreamInterface to be passed directly
            $request = $request->withBody($body);
        } elseif (!empty($options['form_params'])) {
            $request = $request
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($this->streamFactory->createStream(http_build_query($options['form_params'])));
        }

        try {
            return $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            $this->handleException($e);
        }
    }

    /**
     * Parses a NitrAPI response into its payload.
     *
     * Handles rate-limit headers, HTTP error status codes, and JSON unwrapping.
     *
     * @return bool|string|array true when the response is successful but has no payload.
     * @throws NitrapiRateLimitException
     * @throws NitrapiHttpErrorException
     * @throws NitrapiMaintenanceException
     * @throws NitrapiConcurrencyException
     */
    public function parseResponse(ResponseInterface $response)
    {
        // Rate limit metadata
        if ($response->hasHeader('X-RateLimit-Limit')) {
            $this->rateLimit = (int) $response->getHeaderLine('X-RateLimit-Limit');
            $this->remainingRequests = (int) $response->getHeaderLine('X-RateLimit-Remaining');
            $resetDateTime = new DateTime();
            $resetDateTime->setTimestamp((int) $response->getHeaderLine('X-RateLimit-Reset'));
            $this->rateLimitResetTime = $resetDateTime;

            if ($response->getStatusCode() === 429) {
                throw new NitrapiRateLimitException($this->getRateLimit(), $this->getRateLimitResetTime());
            }
        } else {
            $this->rateLimit = false;
        }

        // Map HTTP error status codes to domain exceptions
        $status = $response->getStatusCode();
        if ($status === 503) {
            $msg = $this->extractErrorMessage($response);
            $e = new NitrapiMaintenanceException($msg);
            $e->setResponse($response);
            throw $e;
        }
        if ($status === 428) {
            $msg = $this->extractErrorMessage($response);
            $e = new NitrapiConcurrencyException($msg);
            $e->setResponse($response);
            throw $e;
        }
        if ($status < 200 || ($status >= 300 && $status !== 429)) {
            $msg = $this->extractErrorMessage($response) ?: "Invalid http status code {$status}";
            $e = new NitrapiHttpErrorException($msg);
            $e->setResponse($response);
            $errorId = $response->getHeaderLine('X-Raven-Event-ID');
            if (!empty($errorId)) {
                $e->setErrorId($errorId);
            }
            throw $e;
        }

        $contentType = $response->getHeaderLine('Content-Type');

        // Plain text response
        if (preg_match('#text/plain#i', $contentType)) {
            return $response->getBody()->getContents();
        }

        try {
            $json = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new NitrapiHttpErrorException("Error parsing JSON: {$e->getMessage()}", $e->getCode(), $e);
        }

        // Application-level error in JSON body
        if (is_array($json) && isset($json['status']) && $json['status'] === 'error') {
            throw new NitrapiHttpErrorException($json['message'] ?? 'Unknown error');
        }

        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        if (!empty($json['message'])) {
            return $json['message'];
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // Convenience HTTP methods
    // -------------------------------------------------------------------------

    /**
     * @param string      $url
     * @param array|null  $headers  Additional headers
     * @param array       $options  request() option keys (query, etc.)
     *
     * @return bool|string|array
     *
     * @throws NitrapiException
     */
    public function dataGet(string $url, ?array $headers = null, array $options = [])
    {
        if (is_array($headers)) {
            $options['headers'] = array_merge($options['headers'] ?? [], $headers);
        }
        return $this->parseResponse($this->request('GET', $url, $options));
    }

    /**
     * @param string      $url
     * @param array|null  $body     Form parameters
     * @param array|null  $headers  Additional headers
     * @param array       $options
     *
     * @return bool|string|array
     *
     * @throws NitrapiException
     */
    public function dataPut(string $url, ?array $body = null, ?array $headers = null, array $options = [])
    {
        if (is_array($body)) {
            $options['form_params'] = $body;
        }
        if (is_array($headers)) {
            $options['headers'] = array_merge($options['headers'] ?? [], $headers);
        }
        return $this->parseResponse($this->request('PUT', $url, $options));
    }

    /**
     * @param string      $url
     * @param array|null  $body
     * @param array|null  $headers
     * @param array       $options
     *
     * @return bool|string|array
     *
     * @throws NitrapiException
     */
    public function dataPatch(string $url, ?array $body = null, ?array $headers = null, array $options = [])
    {
        if (is_array($body)) {
            $options['form_params'] = $body;
        }
        if (is_array($headers)) {
            $options['headers'] = array_merge($options['headers'] ?? [], $headers);
        }
        return $this->parseResponse($this->request('PATCH', $url, $options));
    }

    /**
     * @param string      $url
     * @param array|null  $body
     * @param array|null  $headers
     * @param array       $options
     *
     * @return bool|string|array
     *
     * @throws NitrapiException
     */
    public function dataPost(string $url, ?array $body = null, ?array $headers = null, array $options = [])
    {
        if (is_array($body)) {
            $options['form_params'] = $body;
        }
        if (is_array($headers)) {
            $options['headers'] = array_merge($options['headers'] ?? [], $headers);
        }
        return $this->parseResponse($this->request('POST', $url, $options));
    }

    /**
     * @param string      $url
     * @param array|null  $body
     * @param array|null  $headers
     * @param array       $options
     *
     * @return bool|string|array
     *
     * @throws NitrapiException
     */
    public function dataDelete(string $url, ?array $body = null, ?array $headers = null, array $options = [])
    {
        if (is_array($body)) {
            $options['form_params'] = $body;
        }
        if (is_array($headers)) {
            $options['headers'] = array_merge($options['headers'] ?? [], $headers);
        }
        return $this->parseResponse($this->request('DELETE', $url, $options));
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    /**
     * Handles a PSR-18 network-level client exception (connection failures, etc.).
     *
     * @throws NitrapiHttpErrorException
     */
    protected function handleException(ClientExceptionInterface $e): void
    {
        throw new NitrapiHttpErrorException($e->getMessage(), 0, $e);
    }

    /**
     * Attempts to extract a human-readable error message from a response body.
     */
    private function extractErrorMessage(ResponseInterface $response): string
    {
        $body = (string) $response->getBody();
        try {
            $json = @json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return 'Unknown error';
        }
        if (is_array($json) && isset($json['message'])) {
            return $json['message'];
        }
        return $body ?: 'Unknown error';
    }
}
