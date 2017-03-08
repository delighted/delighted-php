<?php

namespace Delighted;

class Unsubscribe extends Resource {

    public static function create($params = array(), Client $client = null) {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->post('unsubscribes', $params);
    }

    public static function all($params = array(), Client $client = null) {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $responses = $client->get('unsubscribes', $params);
        $r = array();
        foreach ($responses as $unsubscribe) {
            $r[] = new Unsubscribe($unsubscribe);
        }
        return $r;
    }
}
