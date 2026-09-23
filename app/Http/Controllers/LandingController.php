<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        return view('landing.home', compact('plans'));
    }

    public function pricing(): View
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        return view('landing.pricing', compact('plans'));
    }
}