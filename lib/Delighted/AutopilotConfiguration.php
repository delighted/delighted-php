<?php

namespace Delighted;

class AutopilotConfiguration extends Resource
{
    protected static $path = 'autopilot';

    public static function retrieve($platform, Client $client = null)
    {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->get(self::$path . '/' . urlencode($platform));
        return new AutopilotConfiguration($response);
    }
}
