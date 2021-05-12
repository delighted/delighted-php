<?php

class AutopilotMembershipTest extends Delighted\TestCase
{

    public function testListAutopilotPeople()
    {
        $persons_1 = [
          [
            'created_at' => 1614041806,
            'updated_at' => 1618012606,
            'person' => [
              'id' => '1',
              'name' => null,
              'email' => 'foo@example.com',
              'created_at' => 1611363406,
              'phone_number' => '+1555555112',
              'last_sent_at' => null
            ],
            'next_survey_request' => [
              'id' => '4',
              'created_at' => 1614041806,
              'survey_scheduled_at' => 1620086206,
              'properties' => [
                'Purchase Experience' => 'Mobile App',
                'State' => 'CA'
              ]
            ]
          ],
          [
            'created_at' => 1614041806,
            'updated_at' => 1618012606,
            'person' => [
              'id' => '2',
              'name' => null,
              'email' => 'foo2@example.com',
              'created_at' => 1611363406,
              'phone_number' => '+1555555113',
              'last_sent_at' => null
            ],
            'next_survey_request' => [
              'id' => '5',
              'created_at' => 1614041806,
              'survey_scheduled_at' => 1620086206,
              'properties' => [
                'Purchase Experience' => 'Mobile App',
                'State' => 'WA'
              ]
            ]
          ]
        ];
        $persons_2 = [
          [
            'created_at' => 1614041806,
            'updated_at' => 1618012606,
            'person' => [
              'id' => '3',
              'name' => null,
              'email' => 'foo3@example.com',
              'created_at' => 1611363406,
              'phone_number' => '+1555555119',
              'last_sent_at' => null
            ],
            'next_survey_request' => [
              'id' => '10',
              'created_at' => 1614041806,
              'survey_scheduled_at' => 1620086206,
              'properties' => [
                'Purchase Experience' => 'Mobile App',
                'State' => 'WA'
              ]
            ]
          ]
        ];
        $next_link = 'https://api.delightedapp.com/v1/autopilot/email/memberships.json?page_info=1234';
        $next_link_header = '<' . $next_link . '>; rel="next"';
        $this->addMultipleMockResponses([
            ['statusCode' => 200, 'headers'=> ['Link' => $next_link_header], 'body' => json_encode($persons_1)],
            ['statusCode' => 200, 'headers'=> [], 'body' => json_encode($persons_2)]
        ]);

        $people = \Delighted\AutopilotMembership\Email::list();
        $this->assertInstanceOf('Delighted\ListResource', $people);
        $result = [];
        foreach ($people->autoPagingIterator() as $person) {
            $result[] = $person;
        }
        $this->assertEquals(3, count($result));

        $first_autopilot = $result[0];
        $this->assertInstanceOf('Delighted\AutopilotMembership', $first_autopilot);
        $this->assertEquals(1, $first_autopilot->person['id']);
        $this->assertEquals('foo@example.com', $first_autopilot->person['email']);
        $this->assertEquals(4, $first_autopilot->next_survey_request['id']);
        $second_autopilot = $result[1];
        $this->assertInstanceOf('Delighted\AutopilotMembership', $second_autopilot);
        $this->assertEquals(2, $second_autopilot->person['id']);
        $this->assertEquals('foo2@example.com', $second_autopilot->person['email']);
        $this->assertEquals(5, $second_autopilot->next_survey_request['id']);
        $third_autopilot = $result[2];
        $this->assertInstanceOf('Delighted\AutopilotMembership', $third_autopilot);
        $this->assertEquals(3, $third_autopilot->person['id']);
        $this->assertEquals('foo3@example.com', $third_autopilot->person['email']);
        $this->assertEquals(10, $third_autopilot->next_survey_request['id']);

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs($next_link, $req);
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testAddPersonEmail()
    {
        $data = [
            'person_id' => 1,
            'person_email' => 'foo@example.com',
            'properties' => [
                'Purchase Experience' => 'Mobile App',
                'State' => 'CA'
            ]
        ];

        $this->addMockResponse(200, json_encode(['person' => ['id' => 1]]));

        $autopilot = \Delighted\AutopilotMembership\Email::create($data);
        $this->assertInstanceOf('Delighted\AutopilotMembership', $autopilot);
        $this->assertEquals(1, $autopilot->person['id']);

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('autopilot/email/memberships', $req);
        $this->assertEquals('POST', $req->getMethod());
        $this->assertRequestParamsEquals($data, $req);
    }

    public function testAddPersonSms()
    {
        $data = [
            'person_id' => 4,
            'person_phone_number' => '+1555555112',
            'properties' => [
                'Purchase Experience' => 'Mobile App',
                'State' => 'CA'
            ]
        ];

        $this->addMockResponse(200, json_encode(['person' => ['id' => 5]]));

        $autopilot = \Delighted\AutopilotMembership\Sms::create($data);
        $this->assertInstanceOf('Delighted\AutopilotMembership', $autopilot);
        $this->assertEquals(5, $autopilot->person['id']);

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('autopilot/sms/memberships', $req);
        $this->assertEquals('POST', $req->getMethod());
        $this->assertRequestParamsEquals($data, $req);
    }

    public function testDeletePerson()
    {
        $data = ['person_email' => 'foo@example.com'];

        $this->addMockResponse(202, json_encode(['person' => ['id' => 1]]));

        $autopilot = \Delighted\AutopilotMembership\Email::delete($data);
        $this->assertInstanceOf('Delighted\AutopilotMembership', $autopilot);
        $this->assertEquals(1, $autopilot->person['id']);

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('autopilot/email/memberships', $req);
        $this->assertEquals('DELETE', $req->getMethod());
    }
}
