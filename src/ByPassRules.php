<?php

namespace Madewithlove\FeatureFlags;

use Madewithlove\FeatureFlags\Models\FeatureFlag;

class ByPassRules
{
    /**
     * @var array
     */
    private $allowedIds;

    /**
     * @param FeatureFlag $featureFlag
     *
     * @return ByPassRules
     */
    public static function fromFeatureFlag(FeatureFlag $featureFlag)
    {
        return new static($featureFlag->bypass_ids ?: []);
    }

    /**
     * ByPassRules constructor.
     *
     * @param array $allowedIds
     */
    public function __construct(array $allowedIds)
    {
        $this->allowedIds = $allowedIds;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data)
    {
        return new static($data['bypass_ids']);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function isIdAllowed($id)
    {
        return in_array($id, $this->allowedIds);
    }
}