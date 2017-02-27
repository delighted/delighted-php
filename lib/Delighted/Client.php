<?php

namespace Delighted;


use Exception;
use GuzzleHttp\Psr7\Request;

require __DIR__ . '/Version.php';

class Client
{

    const DEFAULT_BASE_URL = 'https://api.delighted.com/v1/';
    protected static $instance = null;
    protected static $apiKey = null;

    protected $adapter = null;

    protected function __construct(array $options = [])
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

        $params = [
            'base_uri' => $baseUrl,
            'auth'     => $auth,
            'headers'  => [
                'User-Agent' => 'Delighted PHP API Client ' . \Delighted\VERSION,
                'Accept'     => 'application/json',
            ],
        ];
        if (isset($options['handler'])) {
            $params['handler'] = $options['handler'];
        }
        $this->adapter = new \GuzzleHttp\Client($params);
    }

    public static function getInstance(array $options = null)
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

    public static function get($path, array $params = [])
    {
        $query = self::convertQueryStringToRubyStyle($params);
        $args = ! empty($query) ? ['query' => $query] : [];

        return self::request('get', $path, [], $args);
    }

    public static function convertQueryStringToRubyStyle(array $params = [])
    {
        if (empty($params)) {
            return null;
        }

        // Covert to ruby style notation
        $query = http_build_query($params);
        $string = preg_replace('#%5B(?:[0-9]|[1-9][0-9]+)%5D=#', '%5B%5D=', $query);

        return rawurldecode($string);
    }

    public static function post($path, $params = [])
    {
        return self::request('post', $path, [], ['form_params' => $params]);
    }

    public static function delete($path)
    {
        return self::request('delete', $path);
    }

    public static function put($path, $body = '', $headers = [])
    {
        return self::request('put', $path, $headers, ['body' => $body]);
    }

    protected static function request($method, $path, $headers = [], $argsOrBody = [])
    {
        $instance = self::getInstance();

        try {
            $request = new Request($method, $path, $headers);
            $response = $instance->adapter->send($request, $argsOrBody);

            return json_decode((string) $response->getBody(), true);
        } catch (Exception $e) {
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