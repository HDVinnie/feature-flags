<?php

namespace Madewithlove\FeatureFlags\Listeners;

use Madewithlove\FeatureFlags\RedisManager;
use Madewithlove\FeatureFlags\Events\FlagWasReEnabled;

class ReEnableFlagOnRedis
{
    /**
     * @var RedisManager
     */
    private $redisManager;

    /**
     * ReEnableFlagOnRedis constructor.
     *
     * @param RedisManager $redisManager
     */
    public function __construct(RedisManager $redisManager)
    {
        $this->redisManager = $redisManager;
    }

    public function handle(FlagWasReEnabled $event)
    {
        $this->redisManager->save($event->flag);
    }
}