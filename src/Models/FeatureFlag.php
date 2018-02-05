<?php

namespace Madewithlove\FeatureFlags\Models;

use Illuminate\Database\Eloquent\Model;
use Madewithlove\FeatureFlags\Checkers;
use Madewithlove\FeatureFlags\ByPassRules;
use Madewithlove\FeatureFlags\Checkers\Checker;

class FeatureFlag extends Model
{
    protected $guarded = [];

    protected $casts = [
        'bypass_ids' => 'array',
        'value' => 'boolean',
    ];

    public function checker() : Checkers\Checker
    {
        if ($this->value) {
            return new Checkers\FeatureEnabled();
        }

        if ($this->bypass_ids) {
            return new Checkers\FeatureByPass(ByPassRules::fromFeatureFlag($this));
        }

        return new Checkers\FeatureDisabled();
    }
}
