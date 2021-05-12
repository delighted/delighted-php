<?php

namespace Delighted;

class AutopilotMembership extends Resource
{
    public static function list($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        return new ListResource(get_class(), static::$path, $params, $client);
    }

    public static function create($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->post(static::$path, $params);

        return new AutopilotMembership($response);
    }

    public static function delete($params = [], Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->delete(static::$path, $params);

        return new AutopilotMembership($response);
    }
}
