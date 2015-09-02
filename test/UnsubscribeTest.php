<?php

class UnsubscribeTest extends Delighted\TestCase {

    public function testCreatingUnsubscribe() {
        $data = array('person_email' => 'person@example.com');
        $this->addMockResponse(200, json_encode(array('ok' => true)));

        $response = \Delighted\Unsubscribe::create($data);

        $req = $this->getMockRequest();
        $this->assertEquals('POST', $req->getMethod());
        $this->assertRequestAPIPathIs('unsubscribes', $req);
        $this->assertRequestHeadersOK($req);
    }

    public function testListingUnsubscribes() {
        $data = array(
            array('person_id' => '123', 'email' => 'foo@example.com',
                'name' => 'Foo', 'unsubscribed_at' => 1440621400),
            array('person_id' => '475', 'email' => 'bar@example.com',
                'name' => 'Bar', 'unsubscribed_at' => 1440621830));
        $this->addMockResponse(200, json_encode($data));

        $unsubscribes = \Delighted\Unsubscribe::all();
        $this->assertInternalType('array', $unsubscribes);
        $this->assertEquals(2, count($unsubscribes));
        foreach($unsubscribes as $i => $unsubscribe) {
            $this->assertInstanceOf('\Delighted\Unsubscribe', $unsubscribe);
            foreach ($data[$i] as $k => $v) {
                $this->assertObjectPropertyIs($v, $unsubscribe, $k);
            }
        }

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('unsubscribes', $req);
        $this->assertEquals('GET', $req->getMethod());
    }
}
