<?php

class ClientPublicConstructorForTest extends \Delighted\Client
{
    public function __construct($arg = [])
    {
        parent::__construct($arg);
    }
}

class ClientTest extends Delighted\TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstantiatingClientRequiresApiKey()
    {
        $client = new ClientPublicConstructorForTest([]);
    }

    public function testInstantiatingClientWithApiKey()
    {
        $client = Delighted\Client::getInstance(['apiKey' => '123abc']);
        $this->assertInstanceOf('Delighted\\Client', $client);
    }
}