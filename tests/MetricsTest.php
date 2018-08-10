<?php

namespace Delighted\Tests;

use Delighted\Metrics;

class MetricsTest extends TestCase
{

    public function testRetrievingMetrics()
    {
        $this->addMockResponse(200, json_encode(['nps' => 10]));
        $metrics = Metrics::retrieve();
        $this->assertInstanceOf('Delighted\Metrics', $metrics);
        $this->assertObjectPropertyIs(10, $metrics, 'nps');
        $this->assertObjectNotHasAttribute('id', $metrics);

        $req = $this->getMockRequest();
        $this->assertEquals('GET', $req->getMethod());
        $this->assertRequestAPIPathIs('metrics', $req);
        $this->assertRequestHeadersOK($req);

    }
}
