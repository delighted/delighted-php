<?php

namespace Delighted;

class Bounce extends Resource {
    public static function all($params = array()) {
        $responses = Client::get('bounces', $params);
        $r = array();
        foreach ($responses as $bounce) {
            $r[] = new Bounce($bounce);
        }
        return $r;
    }
}
