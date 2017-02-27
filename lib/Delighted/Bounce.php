<?php

namespace Delighted;

class Bounce extends Resource
{
    public static function all($params = [])
    {
        $responses = Client::get('bounces', $params);
        $r = [];
        foreach ($responses as $bounce) {
            $r[] = new Bounce($bounce);
        }

        return $r;
    }
}
