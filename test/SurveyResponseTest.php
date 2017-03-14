<?php

class SurveyResponseTest extends Delighted\TestCase
{

    public function testCreatingASurveyResponse()
    {
        $data = ['person' => '123', 'score' => 10];
        $this->addMockResponse(200, json_encode(['id' => '456'] + $data));

        $surveyResponse = \Delighted\SurveyResponse::create($data);
        $this->assertInstanceOf('Delighted\SurveyResponse', $surveyResponse);
        $this->assertObjectPropertyIs('123', $surveyResponse, 'person');
        $this->assertObjectPropertyIs(10, $surveyResponse, 'score');
        $this->assertObjectPropertyIs('456', $surveyResponse, 'id');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('survey_responses', $req);
        $this->assertEquals('POST', $req->getMethod());
        $this->assertRequestParamsEquals($data, $req);
    }

    public function testRetrievingASurveyResponse()
    {
        $this->addMockResponse(200, json_encode(['id' => '456', 'person' => '123', 'score' => 10]));

        $surveyResponse = \Delighted\SurveyResponse::retrieve('456');
        $this->assertInstanceOf('Delighted\SurveyResponse', $surveyResponse);
        $this->assertObjectPropertyIs('123', $surveyResponse, 'person');
        $this->assertObjectPropertyIs(10, $surveyResponse, 'score');
        $this->assertObjectPropertyIs('456', $surveyResponse, 'id');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('survey_responses/456', $req);
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testRetrievingASurveyResponseExpandPerson()
    {
        $this->addMockResponse(200, json_encode(['id' => '456', 'person' => ['id' => '123', 'email' => 'foo@bar.com'], 'score' => 10]));

        $surveyResponse = \Delighted\SurveyResponse::retrieve('456', ['expand' => ['person']]);
        $this->assertInstanceOf('Delighted\SurveyResponse', $surveyResponse);
        $this->assertObjectPropertyIs(10, $surveyResponse, 'score');
        $this->assertObjectPropertyIs('456', $surveyResponse, 'id');
        $this->assertInstanceOf('Delighted\Person', $surveyResponse->person);
        $this->assertObjectPropertyIs('123', $surveyResponse->person, 'id');
        $this->assertObjectPropertyIs('foo@bar.com', $surveyResponse->person, 'email');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('survey_responses/456?expand%5B%5D=person', $req);
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testUpdatingASurveyResponse()
    {
        $data = ['person' => '123', 'score' => 10];
        $this->addMockResponse(200, json_encode(['id' => '456'] + $data));

        $surveyResponse = new \Delighted\SurveyResponse([
            'id'     => '456',
            'person' => '321',
            'score'  => 1,
        ]);
        $surveyResponse->person = '123';
        $surveyResponse->score = 10;
        $result = $surveyResponse->save();
        $this->assertInstanceOf('\Delighted\SurveyResponse', $result);
        $this->assertObjectPropertyIs('123', $surveyResponse, 'person');
        $this->assertObjectPropertyIs(10, $surveyResponse, 'score');
        $this->assertObjectPropertyIs('456', $surveyResponse, 'id');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('survey_responses/456', $req);
        $this->assertEquals('PUT', $req->getMethod());
        $this->assertRequestBodyEquals(json_encode($data), $req);
    }

    public function testListingAllSurveyResponses()
    {
        $data = [
            ['id' => '123', 'comment' => 'One'],
            ['id' => '456', 'comment' => 'Two'],
        ];
        $this->addMockResponse(200, json_encode($data));

        $surveyResponses = \Delighted\SurveyResponse::all(['order' => 'desc']);
        $this->assertInternalType('array', $surveyResponses);
        $this->assertEquals(2, count($surveyResponses));
        foreach ($surveyResponses as $i => $surveyResponse) {
            $this->assertInstanceOf('\Delighted\SurveyResponse', $surveyResponse);
            foreach ($data[$i] as $k => $v) {
                $this->assertObjectPropertyIs($v, $surveyResponse, $k);
            }
        }

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('survey_responses?order=desc', $req);
        $this->assertEquals('GET', $req->getMethod());
    }

    public function testListingAllSurveyResponsesExpandPerson()
    {
        $data = [
            ['id' => '123', 'comment' => 'One', 'person' => ['id' => '123', 'email' => 'foo@bar.com']],
            ['id' => '456', 'comment' => 'Two', 'person' => ['id' => '123', 'email' => 'foo@bar.com']],
        ];
        $this->addMockResponse(200, json_encode($data));

        $surveyResponses = \Delighted\SurveyResponse::all(['expand' => ['person']]);
        $this->assertInternalType('array', $surveyResponses);
        $this->assertEquals(2, count($surveyResponses));
        foreach ($surveyResponses as $i => $surveyResponse) {
            $this->assertInstanceOf('\Delighted\SurveyResponse', $surveyResponse);
            foreach ($data[$i] as $k => $v) {
                if ($k == 'person') {
                    $this->assertInstanceOf('\Delighted\Person', $surveyResponses[$i]->$k);
                    foreach ($data[$i][$k] as $k2 => $v2) {
                        $this->assertObjectPropertyIs($v2, $surveyResponses[$i]->$k, $k2);
                    }
                } else {
                    $this->assertObjectPropertyIs($v, $surveyResponse, $k);
                }
            }
        }

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('survey_responses?expand%5B%5D=person', $req);
        $this->assertEquals('GET', $req->getMethod());
    }
}