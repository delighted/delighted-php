<?php

namespace Delighted\Tests;

use Delighted\Client;

class TestClient extends Client
{
    public function getAdapter()
    {
        return $this->httpClient;
    }
}
