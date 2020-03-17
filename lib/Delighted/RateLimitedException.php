<?php

namespace Delighted;

class RateLimitedException extends \Exception {

    public static $errorCode = 429;

    private $retryAfter;

    public function __construct($message, $retryAfter, \Exception $e)
    {
        parent::__construct($message, self::$errorCode, $e);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter()
    {
        return $this->retryAfter;
    }
}
