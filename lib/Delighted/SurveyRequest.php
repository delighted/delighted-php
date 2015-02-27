<?php

namespace Delighted;

class SurveyRequest {
    public static function deletePending($params = array()) {
        if (! isset($params['person_email'])) {
            throw new \InvalidArgumentException("You must pass 'person_email' in argument array");
        }
        return Client::delete('people/' . urlencode($params['person_email']) . '/survey_requests/pending');
    }
}