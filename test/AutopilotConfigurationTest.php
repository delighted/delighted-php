<?php

class AutopilotConfigurationTest extends Delighted\TestCase
{
    public function testRetrieveAutopilotConfiguration()
    {
        $data = [
            'platform_id' => 'email',
            'active' => true,
            'frequency' => 7776000,
            'created_at' => 1611364276,
            'updated_at' => 1618531876
        ];
        $this->addMockResponse(200, json_encode($data));

        $autopilot = \Delighted\AutopilotConfiguration::retrieve('email');
        $this->assertInstanceOf('Delighted\AutopilotConfiguration', $autopilot);
        $this->assertObjectPropertyIs('email', $autopilot, 'platform_id');
        $this->assertObjectPropertyIs(true, $autopilot, 'active');
        $this->assertObjectPropertyIs(7776000, $autopilot, 'frequency');
        $this->assertObjectPropertyIs(1611364276, $autopilot, 'created_at');
        $this->assertObjectPropertyIs(1618531876, $autopilot, 'updated_at');

        $req = $this->getMockRequest();
        $this->assertRequestHeadersOK($req);
        $this->assertRequestAPIPathIs('autopilot/email', $req);
        $this->assertEquals('GET', $req->getMethod());
    }
}
