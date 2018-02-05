<?php

namespace Tests\Unit\FeatureFlags\Checkers;

use App\User;
use Tests\TestCase;
use App\FeatureFlag;
use App\FeatureFlags\ByPassRules;
use App\FeatureFlags\Checkers\FeatureByPass;

class FeatureByPassTest extends TestCase
{
    public function testDeniesFeatureToUsersNotInTheIdsList()
    {
        $user = new User();
        $user->id = 123;

        $feature = new FeatureFlag();
        $checker = new FeatureByPass(ByPassRules::fromFeatureFlag($feature));

        $this->assertFalse($checker->isValidFor($user));
    }

    public function testAllowsUsersByIds()
    {
        $user = new User();
        $user->id = 123;

        $feature = new FeatureFlag();
        $feature->bypass_ids = [23, $user->id];

        $checker = new FeatureByPass(ByPassRules::fromFeatureFlag($feature));

        $this->assertTrue($checker->isValidFor($user));
    }
}
