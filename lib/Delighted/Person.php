<?php

namespace Delighted;

class Person extends Resource {
    public static function create($props = array()) {
        $response = Client::post('people', $props);
        return new Person($response);
    }
}