<?php

namespace Madewithlove\FeatureFlags\Repositories;

use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Illuminate\Database\Eloquent\Collection;

class FeatureFlagsEloquentRepository implements FeatureFlagsRepository
{
    public function save(FeatureFlag $featureFlag)
    {
        $featureFlag->save();
    }

    public function all(): Collection
    {
        return FeatureFlag::all();
    }

    public function update(FeatureFlag $featureFlag, array $data)
    {
        $featureFlag->update($data);
    }

    public function disable(FeatureFlag $flag)
    {
        $flag->update([
            'value' => false,
        ]);
    }

    public function findById($id)
    {
        return FeatureFlag::find($id);
    }

    public function findByFlag($flag)
    {
        return FeatureFlag::where('flag', $flag)
            ->first();
    }

    public function reenable(FeatureFlag $flag)
    {
        $flag->update([
            'value' => true,
        ]);
    }
}