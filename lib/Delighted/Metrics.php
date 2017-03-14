<?php

namespace Delighted;

class Metrics extends Resource
{
    public static function retrieve($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->get('metrics', $params);

        return new Metrics($response);
    }
}
