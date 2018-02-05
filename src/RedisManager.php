<?php

namespace Madewithlove\FeatureFlags;

use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Madewithlove\FeatureFlags\Checkers;
use Illuminate\Contracts\Redis\Factory;

class RedisManager
{
    /**
     * @var Factory
     */
    private $redisFactory;

    /**
     * FeatureFlagsManager constructor.
     *
     * @param Factory $redisFactory
     */
    public function __construct(Factory $redisFactory)
    {
        $this->redisFactory = $redisFactory;
    }

    public function save(FeatureFlag $flag)
    {
        $redis = $this->connection();

        $redis->hmset($flag->flag, [
            'id' => $flag->id,
            'value' => $flag->value,
        ]);

        $this->updateRules($flag);
    }

    public function updateRules(FeatureFlag $flag)
    {
        if (!empty($flag->bypass_ids)) {
            $this->connection()
                ->hmset($flag->flag .':allowed_ids', $flag->bypass_ids ?: []);
        } else {
            $this->connection()->del($flag->flag. ':allowed_ids');
        }
    }

    public function getCheckerForFlag($key)
    {
        if ($this->isEnabled($key)) {
            return new Checkers\FeatureEnabled();
        }

        if ($byPassIds = $this->getByPassFor($key)) {
            return new Checkers\FeatureByPass(ByPassRules::fromArray(['bypass_ids' => $byPassIds]));
        }

        return new Checkers\FeatureDisabled();
    }

    private function getByPassFor($key)
    {
        return $this->connection()
            ->hgetall("$key:allowed_ids") ?: [];
    }

    private function isEnabled($key)
    {
        return !! $this->connection()->hget("$key", "value");
    }

    /**
     * @return \Illuminate\Redis\Connections\Connection
     */
    private function connection()
    {
        return $this->redisFactory->connection('feature-flags');
    }

    public function isCached(string $feature)
    {
        return !!$this->connection()
            ->exists($feature);
    }
}