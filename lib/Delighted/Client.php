<?php

namespace Delighted;
require __DIR__ . '/Version.php';

class Client {

    const DEFAULT_BASE_URL = 'https://api.delighted.com/v1/';
    protected $apiKey = null;
    protected $adapter = null;

    protected static $instance = null;
    protected static $sharedApiKey = null;

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

        $this->adapter = new \Guzzle\Http\Client($baseUrl, array('request.options' => array('auth' => $auth)));
        $this->adapter->setUserAgent('Delighted PHP API Client ' . \Delighted\VERSION);
        $this->adapter->setDefaultOption('headers/Accept', 'application/json');
    }

    public static function getInstance($options = null) {
        if (is_null(self::$instance)) {
            if (!isset($options['apiKey']) && isset(self::$sharedApiKey)) {
                $options['apiKey'] = self::$sharedApiKey;
            }
            self::$instance = new static($options);
            self::$sharedApiKey = $options['apiKey'];
        }
        return self::$instance;
    }

    public static function setApiKey($key) {
        self::$sharedApiKey = $key;
    }

    public function get($path, $params = array()) {
        return $this->request('get', $path, array(), array('query' => $params));
    }

    public function post($path, $params = array()) {
        return $this->request('post', $path, array(), $params);
    }

    public function delete($path) {
        return $this->request('delete', $path);
    }

    public function put($path, $body = '', $headers = array()) {
        return $this->request('put', $path, $headers, $body);
    }

    protected function request($method, $path, $headers = array(), $argsOrBody = array()) {
        $expand = array();
        $request = $this->adapter->$method($path, $headers, $argsOrBody);
        $request->getQuery()->setAggregator(new RailsQueryAggregator);
        try {
            $response = $request->send();
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            $r = $e->getResponse();
            $code = $r->getStatusCode();
            $body = array();
            if (preg_match('#application/json(;|$)#',$r->getContentType())) {
                $body = json_decode((string) $r->getBody(), true);
            }
            throw new RequestException($code, $body, $e);
        }
    }

}
