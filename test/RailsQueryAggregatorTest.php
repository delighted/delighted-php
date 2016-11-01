<?php

class RailsQueryAggregatorTest extends Delighted\TestCase {

    public function testAggregate()
    {
        $value = [
            'num' => ['a', 'b', 'c'],
            'assoc' => ['a' => 'Ann', 'b' => 'Bob'],
        ];

        $query = new \Guzzle\Http\QueryString($value);
        $query->setAggregator(new \Delighted\RailsQueryAggregator());

        $query_string = urldecode((string) $query);

        $this->assertEquals('num[]=a&num[]=b&num[]=c&assoc[a]=Ann&assoc[b]=Bob', $query_string);
    }
}