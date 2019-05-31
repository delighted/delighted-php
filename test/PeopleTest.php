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
        $this->assertInternalType('array', $surveyResponses);
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
}
