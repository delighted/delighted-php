<?php

namespace Delighted;

class Metrics extends Resource
{
    public static function retrieve($params = [])
    {
        $response = Client::get('metrics', $params);

        return new Metrics($response);
    }
}