<?php

namespace Madewithlove\FeatureFlags\Checkers;

class FeatureDisabled implements Checker
{
    public function isValidFor($user = null)
    {
        return false;
    }
}