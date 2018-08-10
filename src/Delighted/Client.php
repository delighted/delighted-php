<?php

namespace Delighted;

use GuzzleHttp\Psr7\Request;
use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Exception\HttpException;
use Http\Discovery\HttpClientDiscovery;
use Http\Client\HttpClient;

require __DIR__ . '/Version.php';

class Client
{
    const DEFAULT_BASE_URL = 'https://api.delighted.com/v1/';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var HttpClient
     */
    protected static $httpClient;

    protected static $instance;
    protected static $sharedApiKey;

    public function __construct(array $options = [], HttpClient $httpClient = null)
    {
        if (! isset($options['apiKey'])) {
            throw new \InvalidArgumentException('No apiKey specified');
        }
        $this->apiKey = $options['apiKey'];

        if (isset($options['baseUrl'])) {
            $this->baseUrl = $options['baseUrl'];
        } else {
            $this->baseUrl = self::DEFAULT_BASE_URL;
        }

        self::setHttpClient($httpClient);
    }

    public static function setHttpClient(HttpClient $httpClient = null)
    {
        $errorPlugin = new ErrorPlugin();

        $pluginClient = new PluginClient(
            $httpClient ?: HttpClientDiscovery::find(),
            [$errorPlugin]
        );

        self::$httpClient = $pluginClient;
    }

    public static function getInstance(array $options = null, HttpClient $httpClient = null)
    {
        if (is_null(self::$instance)) {
            if (! isset($options['apiKey']) && isset(self::$sharedApiKey)) {
                $options['apiKey'] = self::$sharedApiKey;
            }
            self::$instance = new static($options, $httpClient);
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
        return $this->request('get', $path, [], $params);
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
        return $this->request(
            'post',
            $path,
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            [],
            http_build_query($params, null, '&')
        );
    }

    public function delete($path)
    {
        return $this->request('delete', $path);
    }

    public function put($path, $body = null, $headers = [])
    {
        return $this->request('put', $path, $headers, [], $body);
    }

    protected function request($method, $path, $headers = [], $query = [], $body = null)
    {
        $headers['User-Agent']    = 'Delighted PHP API Client ' . \Delighted\VERSION;
        $headers['Accept']        = 'application/json';
        $headers['Authorization'] = 'Basic ' . base64_encode($this->apiKey . ':');

        try {
            $request = new Request($method, $this->getApiUrl($path, $query), $headers, $body);

            $response = self::$httpClient->sendRequest($request);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientErrorException $exception) {
            $response = $exception->getResponse();
            $code = $response->getStatusCode();
            $body = [];
            if (preg_match('#application/json(;|$)#', $response->getHeader('Content-Type')[0])) {
                $body = json_decode($response->getBody()->getContents(), true);
            }
            throw new RequestException($code, $body, $exception);
        } catch (HttpException $exception) {
            $response = $exception->getResponse();
            $code = $response->getStatusCode();
            $body = [];
            if (preg_match('#application/json(;|$)#', $response->getHeader('Content-Type')[0])) {
                $body = json_decode($response->getBody()->getContents(), true);
            }
            throw new RequestException($code, $body, $exception);
        }
    }

    private function getApiUrl($uri, array $query = [])
    {
        return $this->baseUrl . $uri . (!empty($query) ? '?' . $this->convertQueryStringToRubyStyle($query) : '');
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
