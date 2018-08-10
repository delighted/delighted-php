<?php

namespace Delighted\Tests;

class PeopleTest extends TestCase
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
}
