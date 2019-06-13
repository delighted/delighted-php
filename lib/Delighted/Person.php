<?php

namespace Delighted;

class Person extends Resource
{
    public static function create($props = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->post('people', $props);

        return new Person($response);
    }

    public static function delete($idAssoc = array(), Client $client = null) {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $identifier = self::identifierString($idAssoc);
        $path = 'people/' . urlencode($identifier);
        $response = $client->delete($path);
        return $response;
    }

    public static function all($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $r = [];
        $responses = $client->get('people', $params);
        foreach ($responses as $response) {
            $r[] = new Person($response);
        }

        return $r;
    }
}
