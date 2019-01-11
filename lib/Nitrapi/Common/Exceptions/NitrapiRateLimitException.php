<?php

namespace Nitrapi\Common\Exceptions;

/**
 * NitrapiRateLimitException
 *
 * The NitrAPI has some rate limits in place, which limits the number of requests
 * the user can perform in one hour. This limit will be reset every hour, so if
 * the user ran into a rate limit, it will be released by no later than one hour.
 * The rate limit besides the reset time and the remaining tasks will be sent as
 * a header with every response.
 *
 * If the rate limit is reached, the NitrAPI refuses all request. If that happen,
 * we throw this exception, so the client can handle this case properly.
 *
 * @package Nitrapi\Common\Exceptions
 */
class NitrapiRateLimitException extends NitrapiException {
    private $rateLimit;
    private $resetTime;

    public function __construct($rateLimit, $resetTime) {
        $this->rateLimit = $rateLimit;
        $this->resetTime = $resetTime;

        parent::__construct("The rate limit ($rateLimit requests in one hour) is exceeded. You need to wait until $resetTime make another request.");
    }

    public function getRateLimit() {
        return $this->rateLimit;
    }

    public function getResetTime() {
        return $this->resetTime;
    }
}
