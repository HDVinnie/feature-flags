<?php

namespace Madewithlove\FeatureFlags\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('feature-flags::dashboard');
    }
}
