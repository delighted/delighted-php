<?php

namespace Delighted;

class Utils
{
    public static function parse_link_header($link_header)
    {
        // http://docs.guzzlephp.org/en/stable/psr7.html#complex-headers
        $links = \GuzzleHttp\Psr7\parse_header($link_header);
        $result = [];
        foreach ($links as $l) {
            $result[$l['rel']] = trim($l[0], '<>');
        }
        return $result;
    }
}
