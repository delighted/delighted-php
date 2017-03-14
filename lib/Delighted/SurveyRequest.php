<?php

namespace Delighted;

class SurveyRequest
{
    public static function deletePending($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        if (! isset($params['person_email'])) {
            throw new \InvalidArgumentException("You must pass 'person_email' in argument array");
        }

        return $client->delete('people/' . urlencode($params['person_email']) . '/survey_requests/pending');
    }
}
