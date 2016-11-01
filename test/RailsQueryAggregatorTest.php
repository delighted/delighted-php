<?php

class RailsQueryAggregatorTest extends Delighted\TestCase {

    public function testAggregate()
    {
        $value = array(
            'num' => array('a', 'b', 'c'),
            'assoc' => array('a' => 'Ann', 'b' => 'Bob'),
        );

        $query = new \Guzzle\Http\QueryString($value);
        $query->setAggregator(new \Delighted\RailsQueryAggregator());

        $query_string = urldecode((string) $query);

        $this->assertEquals('num[]=a&num[]=b&num[]=c&assoc[a]=Ann&assoc[b]=Bob', $query_string);
    }
}