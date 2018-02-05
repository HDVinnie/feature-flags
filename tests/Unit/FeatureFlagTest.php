<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\FeatureFlag;
use App\FeatureFlags\Checkers;

class FeatureFlagTest extends TestCase
{
    public function testGetsChecker()
    {
        $featureFlag = new FeatureFlag(['value' => true]);

        $this->assertInstanceOf(Checkers\FeatureEnabled::class, $featureFlag->checker());
    }

    public function testReturnsDisabledCheckerWhenNoByPassRuleIsDefined()
    {
        $featureFlag = new FeatureFlag(['value' => false]);

        $this->assertInstanceOf(Checkers\FeatureDisabled::class, $featureFlag->checker());
    }

    public function testReturnsByPassCheckerWhenDisabledAndHasByPassIds()
    {
        $featureFlag = new FeatureFlag([
            'value' => false,
            'bypass_ids' => [23, 43],
        ]);

        $this->assertInstanceOf(Checkers\FeatureByPass::class, $featureFlag->checker());
    }
}
