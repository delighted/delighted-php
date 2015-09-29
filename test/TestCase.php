<?php

namespace Delighted;

class TestCase extends \PHPUnit_Framework_TestCase {

    protected $client;
    protected static $mock = null;

    public function __construct($opts = array()) {
        $this->client = TestClient::getInstance(array('apiKey' => 'abc123'));
        // because the client is a shared (static) instance, we need to ensure
        // we don't add another mock to it for each TestCase instance
        // constructed, so we use a static mock and only add it once.
        if (!self::$mock) {
            self::$mock = new \Guzzle\Plugin\Mock\MockPlugin();
            $this->client->getAdapter()->addSubscriber(self::$mock);
        }
    }

    protected function setUp() {
        self::$mock->clearQueue();
        self::$mock->flush();
    }

    protected function assertObjectPropertyIs($value, $object, $property) {
        $this->assertTrue(isset($object->$property), get_class($object) . " has property $property");
        $this->assertSame($value, $object->$property);
    }

    protected function assertRequestHeadersOK($request) {
        $this->assertEquals('Basic '.base64_encode('abc123:'), (string) $request->getHeader('Authorization'));
        $this->assertEquals('application/json', $request->getHeader('Accept'));
        $this->assertEquals('Delighted PHP API Client ' . \Delighted\VERSION, (string) $request->getHeader('User-Agent'));
    }

    protected function assertRequestBodyEquals($body, $request) {
        $this->assertEquals($body, (string) $request->getBody());
    }

    protected function assertRequestParamsEquals($params, $request) {
        $this->assertEquals(http_build_query($params), (string) $request->getPostFields());
    }

    protected function assertRequestAPIPathIs($path, $request) {
        $this->assertEquals($this->client->getAdapter()->getBaseUrl() . $path, $request->getURL());
    }

    protected function addMockResponse($statusCode, $body = null, $headers = array()) {
        self::$mock->addResponse(new \Guzzle\Http\Message\Response($statusCode, $headers, $body));
    }

    protected function getMockRequest($id = 0) {
        $reqs = self::$mock->getReceivedRequests();
        return $reqs[$id];
    }

}
