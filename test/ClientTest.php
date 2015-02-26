<?php

class ClientTest extends Delighted\TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstantiatingClientRequiresApiKey() {
        $client = new Delighted\Client(array());
    }
    public function testInstantiatingClientWithApiKey() {
        $client = new Delighted\Client(array('apiKey' => '123abc'));
        $this->assertInstanceOf('Delighted\\Client', $client);
    }
}