<?php

namespace Delighted;

use Exception;
use GuzzleHttp\Psr7\Request;

require __DIR__ . '/Version.php';

class Client
{

    const DEFAULT_BASE_URL = 'https://api.delighted.com/v1/';
    protected $apiKey = null;
    protected $adapter = null;

    protected static $instance = null;
    protected static $sharedApiKey = null;

    public function __construct(array $options = [])
    {
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
            $auth = [$this->apiKey, '', 'Basic'];
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
            if (! isset($options['apiKey']) && isset(self::$sharedApiKey)) {
                $options['apiKey'] = self::$sharedApiKey;
            }
            self::$instance = new static($options);
            self::$sharedApiKey = $options['apiKey'];
        }

        return self::$instance;
    }

    public static function setApiKey($key)
    {
        self::$sharedApiKey = $key;
    }

    public function get($path, array $params = [])
    {
        return $this->get_request($path, $params)['body'];
    }

    public function get_request($path, array $params = [])
    {
        $query = $this->convertQueryStringToRubyStyle($params);
        $args = ! empty($query) ? ['query' => $query] : [];

        return $this->request('get', $path, [], $args);
    }

    public function convertQueryStringToRubyStyle(array $params = [])
    {
        if (empty($params)) {
            return null;
        }

        // Covert to ruby style notation
        $query = http_build_query($params);
        $string = preg_replace('#%5B(?:[0-9]|[1-9][0-9]+)%5D=#', '%5B%5D=', $query);

        return rawurldecode($string);
    }

    public function post($path, $params = [])
    {
        return $this->request('post', $path, [], ['form_params' => $params])['body'];
    }

    public function delete($path)
    {
        return $this->request('delete', $path)['body'];
    }

    public function put($path, $body = '', $headers = [])
    {
        return $this->request('put', $path, $headers, ['body' => $body])['body'];
    }

    protected function cleanFormParams($params)
    {
        foreach($params as $key=>$value) {
            if (is_bool($value)) {
                $params[$key] = $value == true ? "true" : "false";
            }
        }

        return $params;
    }

    protected function request($method, $path, $headers = [], $argsOrBody = [])
    {
        try {
            if (array_key_exists('form_params', $argsOrBody)) {
                $argsOrBody['form_params'] = $this->cleanFormParams($argsOrBody['form_params']);
            }
            $request = new Request($method, $path, $headers);
            $response = $this->adapter->send($request, $argsOrBody);

            return ['body' => json_decode((string) $response->getBody(), true),
                'headers' => $response->getHeaders()];
        } catch (Exception $e) {
            $this->handleRequestException($e);
        }
    }

    protected function handleRequestException(Exception $e) {
        $r = $e->getResponse();
        $code = $r->getStatusCode();
        switch ($code) {
            case 429:
                $message = $r->getReasonPhrase();
                $retryAfter = (int)($r->getHeader('Retry-After')[0]);
                throw new RateLimitedException($message, $retryAfter, $e);
            default:
                $body = [];
                if (preg_match('#application/json(;|$)#', $r->getHeader('Content-Type')[0])) {
                    $body = json_decode((string) $r->getBody(), true);
                }
                throw new RequestException($code, $body, $e);
        }
    }

}
