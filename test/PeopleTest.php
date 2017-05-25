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
}
