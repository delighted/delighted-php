<?php

namespace Delighted;

class Unsubscribe extends Resource
{

    public static function create($params = [])
    {
        $response = Client::post('unsubscribes', $params);
    }

    public static function all($params = [])
    {
        $responses = Client::get('unsubscribes', $params);
        $r = [];
        foreach ($responses as $unsubscribe) {
            $r[] = new Unsubscribe($unsubscribe);
        }

        return $r;
    }
}
