<?php

namespace Delighted\Tests;

class BounceTest extends TestCase
{

    public function testListingBounces()
    {
        $data = [
            [
                'person_id'  => '123',
                'email'      => 'foo@example.com',
                'name'       => 'Foo',
                'bounced_at' => 1440621400,
            ],
            [
                'person_id'  => '475',
                'email'      => 'bar@example.com',
                'name'       => 'Bar',
                'bounced_at' => 1440621830,
            ],
        ];
        $this->addMockResponse(200, json_encode($data));

        $bounces = \Delighted\Bounce::all();
        $this->assertInternalType('array', $bounces);
        $this->assertEquals(2, count($bounces));
        foreach ($bounces as $i => $bounce) {
            $this->assertInstanceOf('\Delighted\Bounce', $bounce);
            foreach ($data[$i] as $k => $v) {
                $this->assertObjectPropertyIs($v, $bounce, $k);
            }
        }

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('bounces', $req);
        $this->assertEquals('GET', $req->getMethod());
    }

}
