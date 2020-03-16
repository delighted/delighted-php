<?php

class PeopleTest extends Delighted\TestCase
{

    public function testCreatingOrUpdatingAPerson()
    {
        $data = ['email' => 'foo@bar.com'];
        $this->addMockResponse(200, json_encode(['id' => '123', 'email' => 'foo@bar.com']));

        $person = \Delighted\Person::create($data);
        $this->assertInstanceOf('Delighted\Person', $person);
        $this->assertObjectPropertyIs('foo@bar.com', $person, 'email');
        $this->assertObjectPropertyIs('123', $person, 'id');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('people', $req);
        $this->assertEquals('POST', $req->getMethod());
        $this->assertRequestParamsEquals($data, $req);
    }

    public function testCreatingOrUpdatingAPersonWithoutSending()
    {
        $data = ['email' => 'foo@bar.com', 'send' => false];
        $this->addMockResponse(200, json_encode(['id' => '123', 'email' => 'foo@bar.com']));

        $person = \Delighted\Person::create($data);
        $this->assertInstanceOf('Delighted\Person', $person);
        $this->assertObjectPropertyIs('foo@bar.com', $person, 'email');
        $this->assertObjectPropertyIs('123', $person, 'id');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('people', $req);
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('email=foo%40bar.com&send=false', (string) $req->getBody());
    }

    public function testDeletingPersonByMultipleIdentifiers() {
        try {
            $result = \Delighted\Person::delete(array('id' => 42, 'email' => 'foo@example.com'));
        } catch (Exception $e) {
        }
        $this->assertInstanceOf('InvalidArgumentException', $e);
    }

    public function testDeletingPersonById() {
        $this->addMockResponse(202, json_encode(array('ok' => true)));

        $result = \Delighted\Person::delete(array('id' => 42));
        $this->assertSame(array('ok' => true), $result);
    }

    public function testDeletingPersonByEmail() {
        $this->addMockResponse(202, json_encode(array('ok' => true)));

        $result = \Delighted\Person::delete(array('email' => 'foo@example.com'));
        $this->assertSame(array('ok' => true), $result);
    }

    public function testDeletingPersonByPhoneNumber() {
        $this->addMockResponse(202, json_encode(array('ok' => true)));

        $result = \Delighted\Person::delete(array('phone_number' => '+14155551212'));
        $this->assertSame(array('ok' => true), $result);
    }

    public function testDeletingPendingSurveyRequestsForAPerson()
    {
        $email = 'foo@bar.com';
        $this->addMockResponse(200, json_encode(['ok' => true]));

        $result = \Delighted\SurveyRequest::deletePending(['person_email' => $email]);
        $this->assertSame(['ok' => true], $result);

        $req = $this->getMockRequest();
        $this->assertEquals('DELETE', $req->getMethod());
        $this->assertRequestAPIPathIs('people/' . urlencode($email) . '/survey_requests/pending', $req);
        $this->assertRequestHeadersOK($req);
    }

    public function testListingAllPersonPerPage()
    {
        $data = [
            ['id' => '123', 'email' => 'foo@examle.com'],
            ['id' => '456', 'email' => 'foo2@example.com'],
        ];
        $this->addMockResponse(200, json_encode($data));

        $surveyResponses = \Delighted\Person::all(['per_page' => 2]);
        $this->assertIsArray($surveyResponses);
        $this->assertEquals(2, count($surveyResponses));
        foreach ($surveyResponses as $i => $surveyResponse) {
            $this->assertInstanceOf('\Delighted\Person', $surveyResponse);
            foreach ($data[$i] as $k => $v) {
                $this->assertObjectPropertyIs($v, $surveyResponse, $k);
            }
        }

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('people?per_page=2', $req);
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testListingPersonAutoPaginate()
    {
        $persons_1 = [
            ['id' => '001', 'email' => 'foo@example.com'],
            ['id' => '002', 'email' => 'foo2@example.com'],
            ['id' => '003', 'email' => 'foo3@example.com'],
        ];
        $persons_2 = [
            ['id' => '007', 'email' => 'foo7@example.com'],
            ['id' => '008', 'email' => 'foo8@example.com'],
        ];
        $next_link = 'http://api.delightedapp.com/v1/people.json?page_info=1234';
        $next_link_header = '<' . $next_link . '>; rel="next"';
        $this->addMultipleMockResponses([
            ['statusCode' => 200, 'headers'=> ['Link' => $next_link_header], 'body' => json_encode($persons_1)],
            ['statusCode' => 200, 'headers'=> [], 'body' => json_encode($persons_2)]
        ]);

        $people = \Delighted\Person::list();
        $this->assertInstanceOf('Delighted\ListResource', $people);
        $result = [];
        foreach ($people->autoPagingIterator() as $person) {
            $result[] = $person;
        }
        $this->assertEquals(5, count($result));

        $first_person = $result[0];
        $this->assertInstanceOf('\Delighted\Person', $first_person);
        $this->assertObjectPropertyIs('001', $first_person, 'id');
        $this->assertObjectPropertyIs('foo@example.com', $first_person, 'email');
        $second_person = $result[1];
        $this->assertInstanceOf('\Delighted\Person', $second_person);
        $this->assertObjectPropertyIs('002', $second_person, 'id');
        $this->assertObjectPropertyIs('foo2@example.com', $second_person, 'email');
        $third_person = $result[2];
        $this->assertInstanceOf('\Delighted\Person', $third_person);
        $this->assertObjectPropertyIs('003', $third_person, 'id');
        $this->assertObjectPropertyIs('foo3@example.com', $third_person, 'email');
        $forth_person = $result[3];
        $this->assertInstanceOf('\Delighted\Person', $forth_person);
        $this->assertObjectPropertyIs('007', $forth_person, 'id');
        $this->assertObjectPropertyIs('foo7@example.com', $forth_person, 'email');
        $fifth_person = $result[4];
        $this->assertInstanceOf('\Delighted\Person', $fifth_person);
        $this->assertObjectPropertyIs('008', $fifth_person, 'id');
        $this->assertObjectPropertyIs('foo8@example.com', $fifth_person, 'email');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs($next_link, $req);
        $this->assertEquals('GET', $req->getMethod());
        $this->assertRequestParamsEquals([], $req);
    }

    public function testListingPersonAutoPaginateRateLimited()
    {
        $persons_1 = [
            ['id' => '001', 'email' => 'foo@example.com'],
        ];
        $next_link = 'http://api.delightedapp.com/v1/people.json?page_info=12';
        $next_link_header = '<' . $next_link . '>; rel="next"';
        $this->addMultipleMockResponses([
            ['statusCode' => 200, 'headers'=> ['Link' => $next_link_header], 'body' => json_encode($persons_1)],
            ['statusCode' => 429, 'headers'=> ['Retry-After' => '10'], 'body' => '']
        ]);

        $people = \Delighted\Person::list();
        $this->assertInstanceOf('Delighted\ListResource', $people);
        $result = [];
        try {
            foreach ($people->autoPagingIterator(['auto_handle_rate_limits' => false]) as $person) {
                $result[] = $person;
            }
        } catch (\Delighted\RequestException $exception) {
            $this->assertEquals($exception->getRetryAfter(), '10');
        }

        // Make sure we received an exception
        $this->assertInstanceOf('\Delighted\RequestException', $exception);

        $this->assertEquals(1, count($result));
        $first_person = $result[0];
        $this->assertInstanceOf('\Delighted\Person', $first_person);
        $this->assertObjectPropertyIs('001', $first_person, 'id');
        $this->assertObjectPropertyIs('foo@example.com', $first_person, 'email');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs($next_link, $req);
        $this->assertEquals('GET', $req->getMethod());
        $this->assertRequestParamsEquals([], $req);
    }

    public function testListingPersonAutoPaginateAutoHandleRateLimits()
    {
        $persons_1 = [
            ['id' => '001', 'email' => 'foo@example.com'],
        ];
        $persons_2 = [
            ['id' => '004', 'email' => 'foo4@example.com'],
        ];
        $next_link = 'http://api.delightedapp.com/v1/people.json?page_info=123';
        $next_link_header = '<' . $next_link . '>; rel="next"';
        $this->addMultipleMockResponses([
            ['statusCode' => 200, 'headers'=> ['Link' => $next_link_header], 'body' => json_encode($persons_1)],
            ['statusCode' => 429, 'headers'=> ['Retry-After' => '3'], 'body' => ''],
            ['statusCode' => 200, 'headers'=> [], 'body' => json_encode($persons_2)],
        ]);
        $sleepSpy = $this->getSleepSpy();

        $people = \Delighted\Person::list();
        $this->assertInstanceOf('Delighted\ListResource', $people);
        $result = [];
        try {
            foreach ($people->autoPagingIterator(['auto_handle_rate_limits' => true]) as $person) {
                $result[] = $person;
            }
        } catch (\Delighted\RequestException $exception) {
            // Make sure we did not receive an exception
            $this->assertEquals(true, false);
        }

        // Make sure we got the proper sleep
        $this->assertSleep($sleepSpy, '3');

        $this->assertEquals(2, count($result));
        $first_person = $result[0];
        $this->assertInstanceOf('\Delighted\Person', $first_person);
        $this->assertObjectPropertyIs('001', $first_person, 'id');
        $this->assertObjectPropertyIs('foo@example.com', $first_person, 'email');
        $second_person = $result[1];
        $this->assertInstanceOf('\Delighted\Person', $second_person);
        $this->assertObjectPropertyIs('004', $second_person, 'id');
        $this->assertObjectPropertyIs('foo4@example.com', $second_person, 'email');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs($next_link, $req);
        $this->assertEquals('GET', $req->getMethod());
        $this->assertRequestParamsEquals([], $req);
    }

    public function testListingPersonAutoPaginateSecondCall()
    {
        $persons_1 = [
            ['id' => '001', 'email' => 'foo@example.com'],
        ];
        $this->addMultipleMockResponses([
            ['statusCode' => 200, 'headers'=> [], 'body' => json_encode($persons_1)],
        ]);

        $people = \Delighted\Person::list();
        $this->assertInstanceOf('Delighted\ListResource', $people);
        $result = [];
        foreach ($people->autoPagingIterator() as $person) {
            $result[] = $person;
        }

        $this->assertEquals(1, count($result));
        $first_person = $result[0];
        $this->assertInstanceOf('\Delighted\Person', $first_person);
        $this->assertObjectPropertyIs('001', $first_person, 'id');
        $this->assertObjectPropertyIs('foo@example.com', $first_person, 'email');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('people', $req);
        $this->assertEquals('GET', $req->getMethod());
        $this->assertRequestParamsEquals([], $req);

        $this->expectException('\Delighted\PaginationException');
        foreach ($people->autoPagingIterator() as $person) {
            $result[] = $person;
        }
    }
}
