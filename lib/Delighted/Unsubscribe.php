<?php

namespace Delighted;

class Unsubscribe extends Resource {

    public static function create($params = array()) {
        $response = Client::post('unsubscribes', $params);
    }

    public static function all($params = array()) {
        $responses = Client::get('unsubscribes', $params);
        $r = array();
        foreach ($responses as $unsubscribe) {
            $r[] = new Unsubscribe($unsubscribe);
        }
        return $r;
    }
}
