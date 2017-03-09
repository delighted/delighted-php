<?php

namespace Delighted;

class Bounce extends Resource {
    public static function all($params = array(), Client $client = null) {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $responses = $client->get('bounces', $params);
        $r = array();
        foreach ($responses as $bounce) {
            $r[] = new Bounce($bounce);
        }
        return $r;
    }
}
