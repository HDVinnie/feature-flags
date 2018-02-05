<?php

namespace Madewithlove\FeatureFlags\Listeners;

use Madewithlove\FeatureFlags\RedisManager;
use Madewithlove\FeatureFlags\Events\FlagWasDisabled;

class DisableFlagOnRedis
{
    /**
     * @var RedisManager
     */
    private $redisManager;

    /**
     * DisableFlagOnRedis constructor.
     *
     * @param RedisManager $redisManager
     */
    public function __construct(RedisManager $redisManager)
    {
        $this->redisManager = $redisManager;
    }

    public function handle(FlagWasDisabled $event)
    {
        $this->redisManager->save($event->flag);
    }
}