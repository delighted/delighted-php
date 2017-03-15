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
}
