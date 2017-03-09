<?php

namespace Delighted;

class TestClient extends Client
{
    public function getAdapter()
    {
        return $this->adapter;
    }
}
