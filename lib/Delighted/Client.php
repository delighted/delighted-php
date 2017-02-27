<?php

namespace Delighted;

require __DIR__ . '/Version.php';

class Client
{

    const DEFAULT_BASE_URL = 'https://api.delighted.com/v1/';
    protected static $instance = null;
    protected static $apiKey = null;

    protected $adapter = null;

    protected function __construct($options = [])
    {
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
            $auth = [self::$apiKey, '', 'Basic'];
        }

        $this->adapter = new \Guzzle\Http\Client($baseUrl, ['request.options' => ['auth' => $auth]]);
        $this->adapter->setUserAgent('Delighted PHP API Client ' . \Delighted\VERSION);
        $this->adapter->setDefaultOption('headers/Accept', 'application/json');
    }

    public static function getInstance($options = null)
    {
        if (is_null(self::$instance)) {
            if ((! isset($options['apiKey'])) && isset(self::$apiKey)) {
                $options['apiKey'] = self::$apiKey;
            }
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    public static function setApiKey($key)
    {
        self::$apiKey = $key;
    }


    public static function get($path, $params = [])
    {
        return self::request('get', $path, [], ['query' => $params]);
    }

    public static function post($path, $params = [])
    {
        return self::request('post', $path, [], $params);
    }

    public static function delete($path)
    {
        return self::request('delete', $path);
    }

    public static function put($path, $body = '', $headers = [])
    {
        return self::request('put', $path, $headers, $body);
    }

    protected static function request($method, $path, $headers = [], $argsOrBody = [])
    {
        $instance = self::getInstance();
        $expand = [];
        $request = $instance->adapter->$method($path, $headers, $argsOrBody);
        $request->getQuery()->setAggregator(new RailsQueryAggregator);
        try {
            $response = $request->send();

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            $r = $e->getResponse();
            $code = $r->getStatusCode();
            $body = [];
            if (preg_match('#application/json(;|$)#', $r->getContentType())) {
                $body = json_decode((string) $r->getBody(), true);
            }
            throw new RequestException($code, $body, $e);
        }
    }

}