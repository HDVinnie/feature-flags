<?php

namespace Tests\Feature;

use Tests\IntegrationTestCase;
use Illuminate\Support\Facades\Event;
use Madewithlove\FeatureFlags\FeatureFlags;
use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Madewithlove\FeatureFlags\Events\FlagWasReEnabled;

class CanReEnableFeatureFlagTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFlags::authUsing(function () {
            return true;
        });
    }

    public function tearDown()
    {
        parent::tearDown();

        FeatureFlags::$auth = null;
    }

    public function testCanEnableFeatureFlag()
    {
        Event::fake();
        $featureFlag = factory(FeatureFlag::class)->states(['disabled'])->create();

        $response = $this->postJson(route('enabled-flags.store'), [
            'feature_flag_id' => $featureFlag->getKey(),
            'confirmation' => '1',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'id' => $featureFlag->getKey(),
            'value' => true,
        ]);

        $this->assertTrue($featureFlag->refresh()->value);
        Event::assertDispatched(FlagWasReEnabled::class, function (FlagWasReEnabled $event) use ($featureFlag) {
            return $event->flag->is($featureFlag);
        });
    }

    public function testCannotReEnableWithoutAccepting()
    {
        $featureFlag = factory(FeatureFlag::class)->states(['disabled'])->create();

        $response = $this->postJson(route('enabled-flags.store'), [
            'feature_flag_id' => $featureFlag->getKey(),
        ]);

        $response->assertStatus(422);

        $this->assertFalse($featureFlag->refresh()->value);
    }

    public function testCannotReEnableWithUnknownFeatureFlagId()
    {
        $response = $this->postJson(route('enabled-flags.store'), [
            'feature_flag_id' => 123,
            'confirmation' => '1',
        ]);

        $response->assertStatus(422);
    }
}