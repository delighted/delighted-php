<?php

namespace Delighted;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use \phpmock\spy\Spy;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var TestClient */
    protected $client;

    /** @var \GuzzleHttp\HandlerStack */
    protected static $mock_stack;

    /** @var \GuzzleHttp\Handler\MockHandler */
    protected static $mock_handler;

    public function setUp() {
        $this->client = TestClient::getInstance(['apiKey' => 'abc123', 'handler' => self::$mock_stack]);
    }

    public static function setUpBeforeClass()
    {
        if (! self::$mock_stack) {
            self::$mock_stack = HandlerStack::create(new MockHandler());
        }
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
        $uri = \GuzzleHttp\Psr7\uri_for($path);
        if (!empty($uri->getScheme())) {
            $this->assertEquals((string) $path, (string) $request->getUri());
        } else {
            $this->assertEquals((string) $this->client->getAdapter()->getConfig('base_uri') . $path, (string) $request->getUri());
        }
    }

    protected function addMockResponse($statusCode, $body = null, $headers = [])
    {
        // Create a mock and add response.
        self::$mock_handler = new MockHandler([
            new Response($statusCode, $headers, $body),
        ]);
        self::$mock_stack->setHandler(self::$mock_handler);
    }

    protected function addMultipleMockResponses($mockResponses = [])
    {
        $listResponses = [];
        foreach ($mockResponses as $r) {
            $listResponses[] = new Response($r['statusCode'], $r['headers'], $r['body']);
        }
        self::$mock_handler = new MockHandler($listResponses);
        self::$mock_stack->setHandler(self::$mock_handler);
    }

    /**
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function getMockRequest()
    {
        return self::$mock_handler->getLastRequest();
    }

    public function getSleepSpy()
    {
        $spy = new Spy(__NAMESPACE__, 'sleep', function(){});
        $spy->enable();
        return $spy;
    }

    public function assertSleep(Spy $spy, $sec)
    {
        $this->assertEquals($spy->getInvocations()[0]->getArguments(), [$sec]);
    }
}
