<?php

namespace Madewithlove\FeatureFlags\Repositories;

use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Illuminate\Database\Eloquent\Collection;

interface FeatureFlagsRepository
{
    public function save(FeatureFlag $featureFlag);
    public function all() : Collection;
    public function update(FeatureFlag $featureFlag, array $data);
}