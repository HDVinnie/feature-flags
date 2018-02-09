<?php

namespace Tests\Feature;

use Tests\IntegrationTestCase;
use Illuminate\Support\Facades\Event;
use Madewithlove\FeatureFlags\FeatureFlags;
use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Madewithlove\FeatureFlags\Events\FlagWasDisabled;

class FeatureFlagStatusTest extends IntegrationTestCase
{
    use RefreshDatabase;

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

    public function testCanDisableActiveFeatureFlags()
    {
        Event::fake();
        $featureFlag = factory(FeatureFlag::class)->create([
            'value' => true,
        ]);

        $response = $this->postJson(route('disabled-flags.store'), [
            'feature_flag_id' => $featureFlag->getKey(),
            'confirmation' => '1',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'id' => $featureFlag->getKey(),
            'value' => false,
        ]);

        $this->assertFalse($featureFlag->fresh()->value);
        Event::assertDispatched(FlagWasDisabled::class, function (FlagWasDisabled $event) use ($featureFlag) {
            return $event->flag->is($featureFlag);
        });
    }

    public function testCannotDisableWithoutConfirmation()
    {
        $featureFlag = factory(FeatureFlag::class)->create([
            'value' => true,
        ]);

        $response = $this->postJson(route('disabled-flags.store'), [
            'feature_flag_id' => $featureFlag->getKey(),
        ]);

        $response->assertStatus(422);
        $this->assertTrue($featureFlag->fresh()->value);
    }

    public function testFlagMustExist()
    {
        $response = $this->postJson(route('disabled-flags.store'), [
            'feature_flag_id' => 123,
            'confirmation' => '1',
        ]);

        $response->assertStatus(422);
    }
}