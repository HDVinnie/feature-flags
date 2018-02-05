<?php

namespace Madewithlove\FeatureFlags;

use Madewithlove\FeatureFlags\Repositories\FeatureFlagsEloquentRepository;

class FeatureFlagsManager
{
    /**
     * @var RedisManager
     */
    private $redisManager;

    /**
     * @var FeatureFlagsEloquentRepository
     */
    private $featureFlags;

    /**
     * FeatureFlagsManager constructor.
     *
     * @param RedisManager $redisManager
     * @param FeatureFlagsEloquentRepository $featureFlags
     */
    public function __construct(RedisManager $redisManager, FeatureFlagsEloquentRepository $featureFlags)
    {
        $this->redisManager = $redisManager;
        $this->featureFlags = $featureFlags;
    }

    public function isEnabled(string $feature)
    {
        return $this->isEnabledFor($feature, auth()->user());
    }

    public function isEnabledFor(string $feature, $user) : bool
    {
        $this->updateRedisCacheIfMissing($feature);

        return $this->redisManager->getCheckerForFlag($feature)
            ->isValidFor($user);
    }

    private function updateRedisCacheIfMissing(string $feature)
    {
        $flag = $this->featureFlags->findByFlag($feature);

        if ($flag) {
            $this->redisManager->save($flag);
        }
    }
}