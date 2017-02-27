<?php

namespace Delighted;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var TestClient */
    protected $client;

    /** @var \GuzzleHttp\HandlerStack */
    protected static $mock_stack;

    /** @var \GuzzleHttp\Handler\MockHandler */
    protected static $mock_handler;

    public function __construct($opts = [])
    {
        if (! self::$mock_stack) {
            self::$mock_stack = HandlerStack::create(new MockHandler());
        }
        $this->client = TestClient::getInstance(['apiKey' => 'abc123', 'handler' => self::$mock_stack]);
    }

    protected function setUp()
    {

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
        dump($path);
        dump((string) $request->getUri());
        die;
        $this->assertEquals((string) $this->client->getAdapter()->getConfig('base_uri') . $path, (string) $request->getUri());
    }

    protected function addMockResponse($statusCode, $body = null, $headers = [])
    {
        // Create a mock and add response.
        self::$mock_handler = new MockHandler([
            new Response($statusCode, $headers, $body),
        ]);
        self::$mock_stack->setHandler(self::$mock_handler);
    }

    /**
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function getMockRequest()
    {
        return self::$mock_handler->getLastRequest();
    }
}
