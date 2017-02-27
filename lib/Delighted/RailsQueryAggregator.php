<?php

namespace Delighted;

/**
 * Aggregate nested query string values using Rails style:
 * [] with no indicies for arrays, [] with indicies for hashes
 */
class RailsQueryAggregator extends \Guzzle\Http\QueryAggregator\PhpAggregator implements \Guzzle\Http\QueryAggregator\QueryAggregatorInterface
{
    public function aggregate($key, $value, \Guzzle\Http\QueryString $query)
    {
        $ret = [];
        if ($this->isNumericArray($value)) {
            $ret = $this->numericAggregate($key, $value, $query);
        } else {
            /* For non-numeric arrays, treat them like hashes */
            $ret = parent::aggregate($key, $value, $query);
        }

        return $ret;
    }

    protected function isNumericArray($ar)
    {
        if (! (is_array($ar) && (count($ar) > 0))) {
            return false;
        }
        $keys = array_keys($ar);
        $firstKey = reset($keys);
        $lastKey = end($keys);

        /* Just check first and last, this should be fine except for
         * pathologically broken cases. */

        return (($firstKey === 0) && ($lastKey === (count($ar) - 1)));
    }

    protected function numericAggregate($key, $value, \Guzzle\Http\QueryString $query)
    {
        $subKey = "{$key}[]";
        $values = [];
        foreach ($value as $k => $v) {
            if (is_array($v)) {
                $values[] = $this->aggregate($subKey, $v, $query);
            } else {
                $values[] = $v;
            }
        }

        return [rawurlencode($subKey) => $values];
    }
}