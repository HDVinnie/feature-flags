<?php

namespace Tests;

use Illuminate\Support\Facades\Redis;

class IntegrationTestCase extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Redis::flushall();
    }

    public function tearDown()
    {
        Redis::flushall();

        parent::tearDown();
    }
}