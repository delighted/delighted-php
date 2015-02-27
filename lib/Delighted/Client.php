<?php

namespace Delighted;
require __DIR__ . '/Version.php';

class Client {

    const DEFAULT_BASE_URL = 'https://api.delighted.com/v1/';
    protected static $instance = null;
    protected static $apiKey = null;

    protected $adapter = null;

    protected function __construct($options = array()) {
        if (! isset($options['apiKey'])) {
            throw new \InvalidArgumentException('No apiKey specified');
        }
        self::$apiKey = $options['apiKey'];
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
            $auth = array(self::$apiKey, '', 'Basic');
        }

        $this->adapter = new \Guzzle\Http\Client($baseUrl, array('request.options' => array('auth' => $auth)));
        $this->adapter->setUserAgent('Delighted PHP API Client ' . \Delighted\VERSION);
        $this->adapter->setDefaultOption('headers/Accept', 'application/json');
    }
    public static function getInstance($options = null) {
        if (is_null(self::$instance)) {
            if ((! isset($options['apiKey'])) && isset(self::$apiKey)) {
                $options['apiKey'] = self::$apiKey;
            }
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    public static function setApiKey($key) {
        self::$apiKey = $key;
    }


    public static function get($path, $params = array()) {
        $instance = self::getInstance();
        $request = $instance->adapter->get($path);
        $query = $request->getQuery();
        foreach ($params as $k => $v) {
            $query->add($k, $v);
        }
        $response = $request->send();
        return json_decode((string) $response->getBody(), true);
    }

    public static function post($path, $params = array()) {
        $instance = self::getInstance();
        $request = $instance->adapter->post($path, array(), $params);
        $response = $request->send();
        return json_decode((string) $response->getBody(), true);
    }

    public static function delete($path) {
        $instance = self::getInstance();
        $request = $instance->adapter->delete($path);
        $response = $request->send();
        return json_decode((string) $response->getBody(), true);
    }

    public static function put($path, $body = '', $headers = array()) {
        $instance = self::getInstance();
        $request = $instance->adapter->put($path, $headers, $body);
        $response = $request->send();
        return json_decode((string) $response->getBody(), true);
    }

}