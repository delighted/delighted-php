<?php

class PeopleTest extends Delighted\TestCase {

    public function testCreatingOrUpdatingAPerson() {
        $data = array('email' => 'foo@bar.com');
        $this->addMockResponse(200, json_encode(array('id' => '123', 'email' => 'foo@bar.com')));

        $person = \Delighted\Person::create($data);
        $this->assertInstanceOf('Delighted\Person', $person);
        $this->assertObjectPropertyIs('foo@bar.com', $person, 'email');
        $this->assertObjectPropertyIs('123', $person, 'id');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('/people', $req);
        $this->assertEquals('POST', $req->getMethod());
        $this->assertRequestBodyEquals(json_encode($data), $req);
    }

    public function testUnsubscribingAPerson() {
        $data = array('person_email' => 'person@example.com');
        $this->addMockResponse(200, json_encode(array('ok' => true)));

        $surveyResponse = \Delighted\Unsubscribe::create($data);

        $req = $this->getMockRequest();
        $this->assertEquals('POST', $req->getMethod());
        $this->assertRequestAPIPathIs('/unsubscribes', $req);
        $this->assertRequestHeadersOK($req);
        $this->assertEquals('application/json', (string) $req->getHeader('Content-Type'));

        }

    public function  testDeletingPendingSurveyRequestsForAPerson() {
        $email = 'foo@bar.com';
        $this->addMockResponse(200, json_encode(array('ok' => true)));

        $result = \Delighted\SurveyRequest::deletePending(array('person_email' => $email));
        $this->assertSame(array('ok' => true), $result);

        $req = $this->getMockRequest();
        $this->assertEquals('DELETE', $req->getMethod());
        $this->assertRequestAPIPathIs('/people/'.urlencode($email).'/survey_requests/pending', $req);
        $this->assertRequestHeadersOK($req);
        $this->assertEquals('application/json', (string) $req->getHeader('Content-Type'));

        }
}
