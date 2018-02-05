<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\FeatureFlag;
use App\FeatureFlags\FeatureFlags;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\FeatureFlags\Events\FlagByPassRulesWereUpdated;

class AddByPassRuleToFeatureFlagsTest extends TestCase
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

    public function testUsersCanAddAllowedIdsToByPassRules()
    {
        Event::fake();

        $featureFlag = factory(FeatureFlag::class)->create();
        $this->assertEmpty($featureFlag->bypass_ids);

        $allowedIds = [123, 312];

        $response = $this->putJson(route('flags.update', $featureFlag), [
            'bypass_ids' => $allowedIds,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $featureFlag->getKey(),
            'bypass_ids' => $allowedIds,
        ]);
        $this->assertEquals($allowedIds, $featureFlag->refresh()->bypass_ids);
        Event::assertDispatched(FlagByPassRulesWereUpdated::class, function (FlagByPassRulesWereUpdated $event) use ($featureFlag) {
            return $event->flag->is($featureFlag);
        });
    }

    public function testUsersCanReplaceIdsByPassRulesWithNewList()
    {
        Event::fake();

        $featureFlag = factory(FeatureFlag::class)->create([
            'bypass_ids' => [123, 312],
        ]);

        $allowedIds = [444, 333];

        $response = $this->putJson(route('flags.update', $featureFlag), [
            'bypass_ids' => $allowedIds,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $featureFlag->getKey(),
            'bypass_ids' => $allowedIds,
        ]);

        $this->assertEquals($allowedIds, $featureFlag->refresh()->bypass_ids);
        Event::assertDispatched(FlagByPassRulesWereUpdated::class, function (FlagByPassRulesWereUpdated $event) use ($featureFlag) {
            return $event->flag->is($featureFlag);
        });
    }

    public function testUsersCanReplaceExistingByPassIdsWithAnEmptyList()
    {
        Event::fake();

        $featureFlag = factory(FeatureFlag::class)->create([
            'bypass_ids' => [123, 312],
        ]);

        $allowedIds = [];

        $response = $this->putJson(route('flags.update', $featureFlag), [
            'bypass_ids' => $allowedIds,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $featureFlag->getKey(),
            'bypass_ids' => $allowedIds,
        ]);

        $this->assertEmpty($featureFlag->refresh()->bypass_ids);
        Event::assertDispatched(FlagByPassRulesWereUpdated::class, function (FlagByPassRulesWereUpdated $event) use ($featureFlag) {
            return $event->flag->is($featureFlag);
        });
    }
}