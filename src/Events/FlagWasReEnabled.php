<?php

namespace Madewithlove\FeatureFlags\Events;

use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class FlagWasReEnabled
{
    use Dispatchable, SerializesModels;

    /**
     * @var FeatureFlag
     */
    public $flag;

    /**
     * Create a new event instance.
     *
     * @param FeatureFlag $flag
     */
    public function __construct(FeatureFlag $flag)
    {
        //
        $this->flag = $flag;
    }
}
