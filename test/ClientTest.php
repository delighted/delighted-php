<?php

class ClientTest extends Delighted\TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstantiatingClientRequiresApiKey()
    {
        $client = new Delighted\Client([]);
    }

    public function testInstantiatingClientWithApiKey()
    {
        $client = Delighted\Client::getInstance(['apiKey' => '123abc']);
        $this->assertInstanceOf('Delighted\\Client', $client);
    }

    public function testAggregate()
    {
        $value = [
            'num'   => ['a', 'b', 'c'],
            'assoc' => ['a' => 'Ann', 'b' => 'Bob'],
        ];

        $query_string = $this->client->convertQueryStringToRubyStyle($value);

        $this->assertEquals('num[]=a&num[]=b&num[]=c&assoc[a]=Ann&assoc[b]=Bob', $query_string);
    }

    public function testRateLimitErrorResponse()
    {
        $client = Delighted\Client::getInstance(['apiKey' => '123abc']);
        $this->addMockResponse(429, null, ['Content-Type' => 'text/plain', 'Retry-After' => '5']);

        try {
            $response = $client->get('/foo');
        } catch (Exception $e) {
        }
        $this->assertInstanceOf('Delighted\\RequestException', $e);
        $this->assertEquals(5, $e->getRetryAfter());
        $this->assertEquals(429, $e->getCode());
    }
}
