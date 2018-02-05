<?php

namespace Madewithlove\FeatureFlags\Checkers;

class FeatureEnabled implements Checker
{
    /**
     * @param $user
     *
     * @return bool
     */
    public function isValidFor($user = null)
    {
        return true;
    }
}