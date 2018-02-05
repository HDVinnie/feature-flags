<?php

namespace Madewithlove\FeatureFlags\Events;

use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class FlagWasCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var FeatureFlag
     */
    public $featureFlag;

    /**
     * Create a new event instance.
     *
     * @param FeatureFlag $featureFlag
     */
    public function __construct(FeatureFlag $featureFlag)
    {
        $this->featureFlag = $featureFlag;
    }
}
