<?php

namespace Delighted;

class SurveyResponse extends Resource
{

    protected $expandable = ['person' => '\Delighted\Person'];

    public static function create($props = [])
    {
        $response = Client::post('survey_responses', $props);

        return new SurveyResponse($response);
    }

    public static function retrieve($id, $params = [])
    {
        if (empty($id)) {
            throw new \InvalidArgumentException("You must pass a survey response ID to retrieve");
        }
        $path = 'survey_responses/' . urlencode($id);
        $response = Client::get($path, $params);

        return new SurveyResponse($response);
    }

    public static function all($params = [])
    {
        $r = [];
        $responses = Client::get('survey_responses', $params);
        foreach ($responses as $response) {
            $r[] = new SurveyResponse($response);
        }

        return $r;
    }

    public function save()
    {
        $params = $this->__data;
        $path = 'survey_responses/' . urlencode($params['id']);
        unset($params['id']);
        $response = Client::put($path, json_encode($this->doJsonSerialize($params)), ['Content-Type' => 'application/json']);

        return new SurveyResponse($response);
    }

}