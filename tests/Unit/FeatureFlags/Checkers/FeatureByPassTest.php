<?php

namespace Tests\Unit\FeatureFlags\Checkers;

use Tests\TestCase;
use Madewithlove\FeatureFlags\ByPassRules;
use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Madewithlove\FeatureFlags\Checkers\FeatureByPass;

class FeatureByPassTest extends TestCase
{
    public function testDeniesFeatureToUsersNotInTheIdsList()
    {
        $user = new DummyUser();
        $user->id = 123;

        $feature = new FeatureFlag();
        $checker = new FeatureByPass(ByPassRules::fromFeatureFlag($feature));

        $this->assertFalse($checker->isValidFor($user));
    }

    public function testAllowsUsersByIds()
    {
        $user = new DummyUser();
        $user->id = 123;

        $feature = new FeatureFlag();
        $feature->bypass_ids = [23, $user->id];

        $checker = new FeatureByPass(ByPassRules::fromFeatureFlag($feature));

        $this->assertTrue($checker->isValidFor($user));
    }
}

class DummyUser
{
    public $id;

    public function getKey()
    {
        return $this->id;
    }
}
