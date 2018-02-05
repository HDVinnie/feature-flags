<?php

namespace Madewithlove\FeatureFlags\Checkers;

interface Checker
{
    public function isValidFor($user = null);
}