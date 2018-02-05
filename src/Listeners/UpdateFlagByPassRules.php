<?php

namespace Madewithlove\FeatureFlags\Listeners;

use Madewithlove\FeatureFlags\RedisManager;
use Madewithlove\FeatureFlags\Events\FlagByPassRulesWereUpdated;

class UpdateFlagByPassRules
{
    /**
     * @var RedisManager
     */
    private $redisManager;

    /**
     * UpdateFlagByPassRules constructor.
     *
     * @param RedisManager $redisManager
     */
    public function __construct(RedisManager $redisManager)
    {
        $this->redisManager = $redisManager;
    }

    /**
     * @param FlagByPassRulesWereUpdated $event
     */
    public function handle(FlagByPassRulesWereUpdated $event)
    {
        $this->redisManager->updateRules($event->flag);
    }
}