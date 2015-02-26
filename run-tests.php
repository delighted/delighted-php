#!/usr/bin/env php
<?php

chdir(__DIR__);

passthru('composer install', $rv);
if (0 !== $rv) {
    exit(1);
}

passthru('./vendor/bin/phpunit -c phpunit.xml', $rv);
if (0 !== $rv) {
    exit(1);
}
