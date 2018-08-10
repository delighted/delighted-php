<?php

namespace Delighted\Tests;

use Delighted\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;

class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var Client */
    protected $client;

    /**
     * @var \Http\Mock\Client
     */
    protected $mockClient;

    public function __construct($opts = [])
    {
        $this->client = Client::getInstance(['apiKey' => 'abc123']);
    }

    protected function setUp()
    {
        $this->mockClient = new \Http\Mock\Client();
        Client::setHttpClient($this->mockClient);
    }

    protected function assertObjectPropertyIs($value, $object, $property)
    {
        $this->assertTrue(isset($object->$property), get_class($object) . " has property $property");
        $this->assertSame($value, $object->$property);
    }

    protected function assertRequestHeadersOK(Request $request)
    {
        $this->assertEquals('Basic ' . base64_encode('abc123:'), $request->getHeader('Authorization')[0]);
        $this->assertEquals('application/json', $request->getHeader('Accept')[0]);
        $this->assertEquals('Delighted PHP API Client ' . \Delighted\VERSION, $request->getHeader('User-Agent')[0]);
    }

    protected function assertRequestBodyEquals($body, Request $request)
    {
        $this->assertEquals($body, (string) $request->getBody());
    }

    protected function assertRequestParamsEquals($params, Request $request)
    {
        $this->assertEquals(http_build_query($params), (string) $request->getBody());
    }

    protected function assertRequestAPIPathIs($path, Request $request)
    {
        $this->assertEquals((string) $this->client->getBaseUrl() . $path, (string) $request->getUri());
    }

    protected function addMockResponse($statusCode, $body = null, $headers = [])
    {
        $this->mockClient->addResponse(new Response($statusCode, $headers, $body));
    }

    /**
     * @return RequestInterface|false
     */
    protected function getMockRequest()
    {
        return $this->mockClient->getLastRequest();
    }
}
