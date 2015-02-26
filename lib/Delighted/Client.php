<?php

namespace Delighted;

class Client extends \Guzzle\Http\Client {

    const DEFAULT_BASE_URL = 'https://api.delightedapp.com/v1/';
    protected static $client = null;

    public function __construct($options = array()) {
        if (! isset($options['apiKey'])) {
            throw new \InvalidArgumentException('No apiKey specified');
        }
        $this->apiKey = $options['apiKey'];
        unset($options['apiKey']);

        if (isset($options['baseUrl'])) {
            $baseUrl = $options['baseUrl'];
            unset($options['baseUrl']);
        } else {
            $baseUrl = self::DEFAULT_BASE_URL;
        }


        if (isset($options['auth'])) {
            $auth = $options['auth'];
        } else {
            $auth = array($this->apiKey, '', 'Basic');
        }

        parent::__construct($baseUrl, array('request.options' => array('auth' => $auth)));
        $this->setUserAgent('Delighted PHP API Client');
    }

    public static function getClient($options = null) {
        if (is_null(self::$client)) {
            self::$client = new Client($options);
        }
        return self::$client;
    }

}