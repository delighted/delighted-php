<?php

namespace Delighted;

class Autopilot extends Resource
{
    protected static $path = 'autopilot/DELIGHTED_PLATFORM';
    protected static $pathMembership = 'autopilot/DELIGHTED_PLATFORM/memberships';
    protected static $platforms = ['email', 'sms'];

    public static function getConfiguration($platform, Client $client = null)
    {
        self::validatePlatform($platform);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = self::getPath(self::$path, $platform);
        $response = $client->get($path);
        return new Autopilot($response);
    }

    public static function listPeople($platform, $params = [], Client $client = null)
    {
        self::validatePlatform($platform);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = self::getPath(self::$pathMembership, $platform);
        return new ListResource(get_class(), $path, $params, $client);
    }

    public static function addPerson($platform, $params = [], Client $client = null)
    {
        self::validatePlatform($platform);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = self::getPath(self::$pathMembership, $platform);
        $response = $client->post($path, $params);

        return new Autopilot($response);
    }

    public static function deletePerson($platform, $params = [], Client $client = null)
    {
        self::validatePlatform($platform);

        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $path = self::getPath(self::$pathMembership, $platform);
        $response = $client->delete($path, $params);

        return new Autopilot($response);
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
