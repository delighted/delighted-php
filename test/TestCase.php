<?php

namespace Delighted;

class TestCase extends \PHPUnit_Framework_TestCase {

    protected $client;
    protected $mock;

    public function __construct($opts = array()) {
        $this->client = Client::getClient(array('apiKey' => 'abc123'));
        $this->mock = new \Guzzle\Plugin\Mock\MockPlugin();
        $this->client->addSubscriber($this->mock);
    }

    protected function assertObjectPropertyIs($value, $object, $property) {
        $this->assertObjectHasAttribute($property, $object);
        $this->assertSame($value, $object->$property);
    }

    protected function assertRequestHeadersOK($request) {
        $this->assertEquals('Basic '.base64_encode('abc123:'), (string) $request->getHeader('Authorization'));
        $this->assertEquals('application/json', $request->getHeader('Accept'));
        $this->assertEquals('Delighted PHP API Client', (string) $request->getHeader('User-Agent'));
    }

    protected function assertRequestBodyEquals($body, $request) {
        $this->assertEquals($body, (string) $request->getBody());
    }

    protected function assertRequestAPIPathIs($path, $request) {
        $this->assertEquals($this->client->getBaseUrl() . $path, $request->getURL());
    }

    protected function addMockResponse($statusCode, $body = null, $headers = array()) {
        $this->mock->addResponse(new \Guzzle\Http\Message\Response($statusCode, $headers, $body));
    }

    protected function getMockRequest($id = 0) {
        $reqs = $this->mock->getReceivedRequests();
        return $reqs[$id];
    }

}
