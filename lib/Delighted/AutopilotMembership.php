<?php

namespace Delighted;

class AutopilotMembership extends Resource
{
    protected static $path = 'autopilot/DELIGHTED_PLATFORM/memberships';
    protected static $platforms = ['email', 'sms'];

    public static function list($platform, $params = [], Client $client = null)
    {
        self::validatePlatform($platform);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = self::getPath(self::$path, $platform);
        return new ListResource(get_class(), $path, $params, $client);
    }

    public static function create($platform, $params = [], Client $client = null)
    {
        self::validatePlatform($platform);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = self::getPath(self::$path, $platform);
        $response = $client->post($path, $params);

        return new AutopilotMembership($response);
    }

    public static function delete($platform, $params = [], Client $client = null)
    {
        self::validatePlatform($platform);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = self::getPath(self::$path, $platform);
        $response = $client->delete($path, $params);

        return new AutopilotMembership($response);
    }

    private static function validatePlatform($platform)
    {
        if (!in_array($platform, self::$platforms)) {
            throw new \InvalidArgumentException("Invalid platform");
        }
    }

    private static function getPath($path, $platform)
    {
        return str_replace('DELIGHTED_PLATFORM', urlencode($platform), $path);
    }
}
