<?php

namespace Tests\Integration;

use App\FeatureFlag;
use App\FeatureFlags\Checkers;
use Tests\IntegrationTestCase;
use App\FeatureFlags\RedisManager;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CachesFeatureFlagsTest extends IntegrationTestCase
{
    use DatabaseMigrations;

    public function testCanCacheEnabledFlag()
    {
        $featureFlag = factory(FeatureFlag::class)->create([
            'flag' => 'LOREM',
            'value' => true,
        ]);

        $repository = resolve(RedisManager::class);

        $repository->save($featureFlag);

        $this->assertTrue(!!Redis::exists('feature-flags:LOREM'));
        $this->assertTrue(!!Redis::hget('feature-flags:LOREM', 'value'));
    }

    public function testCanCacheDisabledFlag()
    {
        $featureFlag = factory(FeatureFlag::class)->create([
            'flag' => 'LOREM',
            'value' => false,
        ]);

        $repository = resolve(RedisManager::class);

        $repository->save($featureFlag);

        $this->assertTrue(!!Redis::exists('feature-flags:LOREM'));
        $this->assertFalse(!!Redis::hget('feature-flags:LOREM', 'value'));
    }

    public function testCanCacheByPassIds()
    {
        $featureFlag = factory(FeatureFlag::class)->create([
            'flag' => 'LOREM',
            'bypass_ids' => [123, 321],
        ]);

        $repository = resolve(RedisManager::class);

        $repository->save($featureFlag);

        $this->assertTrue(!!Redis::exists('feature-flags:LOREM'));
        $this->assertEquals([123, 321], Redis::hgetall('feature-flags:LOREM:allowed_ids'));
    }

    public function testRemovingFeatureFlagByPassIdsRemovesTheRedisKey()
    {
        $featureFlag = factory(FeatureFlag::class)->create([
            'flag' => 'LOREM',
            'bypass_ids' => [123, 312],
        ]);

        $repository = resolve(RedisManager::class);
        $repository->save($featureFlag);

        $featureFlag->update([
            'bypass_ids' => [],
        ]);

        $repository->save($featureFlag);

        $this->assertTrue(!!Redis::exists('feature-flags:LOREM'));
        $this->assertEquals([], Redis::hgetall('feature-flags:LOREM:allowed_ids'));
    }

    public function testGetsFeatureDisabledWhenNoFlagNotFoundOnRedis()
    {
        $repository = resolve(RedisManager::class);

        $this->assertInstanceOf(Checkers\FeatureDisabled::class, $repository->getCheckerForFlag('LOREM'));
    }

    public function testGetsFeatureEnabledFromRedis()
    {
        $featureFlag = factory(FeatureFlag::class)->create([
            'flag' => 'LOREM',
            'value' => true,
        ]);

        $repository = resolve(RedisManager::class);
        $repository->save($featureFlag);

        $this->assertInstanceOf(Checkers\FeatureEnabled::class, $repository->getCheckerForFlag('LOREM'));
    }

    public function testGetsByPassWhenFeatureDisabledByPassRegistered()
    {
        $featureFlag = factory(FeatureFlag::class)->create([
            'flag' => 'LOREM',
            'value' => false,
            'bypass_ids' => [123, 321],
        ]);

        $repository = resolve(RedisManager::class);
        $repository->save($featureFlag);

        $this->assertInstanceOf(Checkers\FeatureByPass::class, $repository->getCheckerForFlag('LOREM'));
    }
}