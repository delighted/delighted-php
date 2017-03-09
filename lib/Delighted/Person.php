<?php

namespace Delighted;

class Person extends Resource {
    public static function create($props = array(), Client $client = null) {
        if (is_null($client)) {
            $client = Client::getInstance();
        }

        $response = $client->post('people', $props);
        return new Person($response);
    }
}
