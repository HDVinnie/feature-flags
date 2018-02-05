<?php

namespace Madewithlove\FeatureFlags\Checkers;

use Madewithlove\FeatureFlags\ByPassRules;

class FeatureByPass implements Checker
{
    /**
     * @var ByPassRules
     */
    private $byPassRules;

    /**
     * FeatureByPass constructor.
     *
     * @param ByPassRules $byPassRules
     */
    public function __construct(ByPassRules $byPassRules)
    {
        $this->byPassRules = $byPassRules;
    }

    public function isValidFor($user = null)
    {
        if (!$user) {
            return false;
        }

        return $this->byPassRules->isIdAllowed($user->getKey());
    }
}