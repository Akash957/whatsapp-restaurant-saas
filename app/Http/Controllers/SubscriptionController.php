<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request): View {
        $restaurant = $request->user()->restaurant->load('subscriptions.plan');
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        $current = $restaurant->subscriptions->firstWhere(fn($s)=> in_array($s->status,['trial','active']) && $s->ends_at->isFuture());
        return view('panel.subscriptions.index', compact('restaurant','plans','current'));
    }
    public function update(Request $request) {
        $data = $request->validate(['plan_id'=>'required|exists:subscription_plans,id']);
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $restaurant = $request->user()->restaurant;
        $restaurant->subscriptions()->create(['subscription_plan_id'=>$plan->id,'status'=>'active','starts_at'=>now(),'ends_at'=>now()->addDays($plan->duration_days)]);
        return back()->with('success','Subscription updated to '.$plan->name);
    }
}