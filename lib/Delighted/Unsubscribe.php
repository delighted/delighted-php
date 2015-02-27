<?php

namespace Delighted;

class Unsubscribe {

    public static function create($params = array()) {
        $response = Client::post('unsubscribes', $params);
    }
}