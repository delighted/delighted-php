<?php

namespace Delighted;

class SurveyResponse extends Resource
{

    protected $expandable = ['person' => '\Delighted\Person'];

    public static function create($props = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->post('survey_responses', $props);

        return new SurveyResponse($response);
    }

    public static function retrieve($id, $params = [], Client $client = null)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException("You must pass a survey response ID to retrieve");
        }

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = 'survey_responses/' . urlencode($id);
        $response = $client->get($path, $params);

        return new SurveyResponse($response);
    }

    public static function all($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $r = [];
        $responses = $client->get('survey_responses', $params);
        foreach ($responses as $response) {
            $r[] = new SurveyResponse($response);
        }

        return $r;
    }

    public function save(Client $client = null)
    {
        $params = $this->__data;
        $path = 'survey_responses/' . urlencode($params['id']);
        unset($params['id']);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->put($path, json_encode($this->doJsonSerialize($params)), ['Content-Type' => 'application/json']);

        return new SurveyResponse($response);
    }

}
