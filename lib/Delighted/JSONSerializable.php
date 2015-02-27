<?php

namespace Delighted;

if (! interface_exists('JSONSerializable')) {
    interface JSONSerializable {
        public function jsonSerialize();
    }
} else {
    interface JSONSerializable extends \JSONSerializable {}
}