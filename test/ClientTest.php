<?php

class ClientPublicConstructorForTest extends \Delighted\Client {
    public function __construct($arg = array()) {
        parent::__construct($arg);
    }
}

class ClientTest extends Delighted\TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstantiatingClientRequiresApiKey() {
        $client = new ClientPublicConstructorForTest(array());
    }
    public function testInstantiatingClientWithApiKey() {
        $client = Delighted\Client::getInstance(array('apiKey' => '123abc'));
        $this->assertInstanceOf('Delighted\\Client', $client);
    }
}