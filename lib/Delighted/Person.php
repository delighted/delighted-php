<?php

namespace Delighted;

class Person extends Resource
{
    protected static $path = 'people';

    public static function create($props = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->post(self::$path, $props);

        return new Person($response);
    }

    public static function delete($idAssoc = array(), Client $client = null) {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $identifier = self::identifierString($idAssoc);
        $path = self::$path . '/' . urlencode($identifier);
        return $client->delete($path);
    }

    public static function all($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $r = [];
        $responses = $client->get(self::$path, $params);
        foreach ($responses as $response) {
            $r[] = new Person($response);
        }

        return $r;
    }

    public static function list($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }
        return new ListResource(get_class(), self::$path, $params, $client);
    }
}
