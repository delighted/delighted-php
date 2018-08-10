<?php

namespace Delighted;

use Http\Client\Exception\HttpException;

class RequestException extends \Exception
{

    protected $data;
    protected $response;

    public function __construct($code, $data = [], HttpException $e)
    {
        parent::__construct($e->getResponse()->getReasonPhrase(), $code, $e);
        $this->data = $data;
        $this->response = $e->getResponse();
    }

    public function getData()
    {
        return $this->data;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRetryAfter()
    {
        return (int)($this->getResponse()->getHeader('Retry-After')[0]);
    }

}
