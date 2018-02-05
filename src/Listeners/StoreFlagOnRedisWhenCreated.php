<?php

namespace Madewithlove\FeatureFlags\Listeners;

use Madewithlove\FeatureFlags\RedisManager;
use Madewithlove\FeatureFlags\Events\FlagWasCreated;

class StoreFlagOnRedisWhenCreated
{
    /**
     * @var RedisManager
     */
    private $redisManager;

    /**
     * StoreFlagOnRedisWhenCreated constructor.
     *
     * @param RedisManager $redisManager
     */
    public function __construct(RedisManager $redisManager)
    {
        $this->redisManager = $redisManager;
    }

    /**
     * @param FlagWasCreated $event
     */
    public function handle(FlagWasCreated $event)
    {
        $this->redisManager->save($event->featureFlag);
    }
}