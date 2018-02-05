<?php

namespace Madewithlove\FeatureFlags\Http\Controllers;

use Illuminate\Http\Request;
use Madewithlove\FeatureFlags\Models\FeatureFlag;
use Madewithlove\FeatureFlags\Events;
use Madewithlove\FeatureFlags\Repositories\FeatureFlagsEloquentRepository;

class FeatureFlagsController extends Controller
{
    /**
     * @var \Madewithlove\FeatureFlags\Repositories\FeatureFlagsEloquentRepository
     */
    private $featureFlagRepository;

    /**
     * FeatureFlagsController constructor.
     *
     * @param FeatureFlagsEloquentRepository $featureFlagRepository
     */
    public function __construct(FeatureFlagsEloquentRepository $featureFlagRepository)
    {
        parent::__construct();

        $this->featureFlagRepository = $featureFlagRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $featureFlags = $this->featureFlagRepository->all();

        return response()->json($featureFlags);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'flag' => [
                'required',
                'string',
                'unique:feature_flags,flag',
            ],
            'value' => 'required|boolean',
            'description' => 'string|nullable',
        ]);

        $flag = new FeatureFlag($data);

        $this->featureFlagRepository->save($flag);

        event(new Events\FlagWasCreated($flag));

        return response()->json($flag, 201);
    }

    /**
     * @param Request $request
     * @param FeatureFlag $flag
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, FeatureFlag $flag)
    {
        $data = $this->validate($request, [
            'bypass_ids.*' => [
                'numeric',
            ],
        ]);

        $this->featureFlagRepository->update($flag, $data);

        event(new Events\FlagByPassRulesWereUpdated($flag));

        return response()->json($flag, 200);
    }
}
