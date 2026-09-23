<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RestaurantSettingsController extends Controller
{
    public function index(Request $request): View {
        $restaurant = $request->user()->restaurant->load('deliverySetting','businessHours','whatsappSetting');
        return view('panel.settings.edit', compact('restaurant'));
    }
    public function update(Request $request): RedirectResponse {
        $restaurant = $request->user()->restaurant;
        $data = $request->validate([
            'name'=>'sometimes|string|max:255','description'=>'nullable|string','phone'=>'nullable|string|max:30','whatsapp_number'=>'nullable|string|max:30','email'=>'nullable|email',
            'address'=>'nullable|string','city'=>'nullable|string','state'=>'nullable|string','country'=>'nullable|string','postal_code'=>'nullable|string',
            'primary_color'=>'nullable|string','secondary_color'=>'nullable|string','button_color'=>'nullable|string','text_color'=>'nullable|string',
            'facebook'=>'nullable|url','instagram'=>'nullable|url','youtube'=>'nullable|url','website'=>'nullable|url',
            'enable_delivery'=>'boolean','enable_pickup'=>'boolean','enable_dine_in'=>'boolean','minimum_order'=>'nullable|integer','delivery_charge'=>'nullable|integer','free_delivery_above'=>'nullable|integer','tax_rate'=>'nullable|numeric',
        ]);
        $restaurant->update(collect($data)->except(['enable_delivery','enable_pickup','enable_dine_in','minimum_order','delivery_charge','free_delivery_above','tax_rate'])->toArray());
        if($restaurant->deliverySetting) {
            $restaurant->deliverySetting->update([
                'enable_delivery'=>$request->boolean('enable_delivery', true),
                'enable_pickup'=>$request->boolean('enable_pickup', true),
                'enable_dine_in'=>$request->boolean('enable_dine_in', true),
                'minimum_order'=>$request->input('minimum_order',0),
                'delivery_charge'=>$request->input('delivery_charge',0),
                'free_delivery_above'=>$request->input('free_delivery_above'),
                'tax_rate'=>$request->input('tax_rate',5),
            ]);
        }
        return back()->with('success','Settings updated.');
    }
}