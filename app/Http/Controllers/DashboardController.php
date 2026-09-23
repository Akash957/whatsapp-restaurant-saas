<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WhatsAppSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total_restaurants' => Restaurant::count(),
            'active_restaurants' => Restaurant::where('status', 'active')->count(),
            'pending_restaurants' => Restaurant::where('status', 'pending')->count(),
            'total_customers' => Customer::count(),
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->sum('total'),
            'monthly_revenue' => Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total'),
            'active_subscriptions' => Subscription::whereIn('status', ['active', 'trial'])->where('ends_at', '>', now())->count(),
            'expired_subscriptions' => Subscription::where('status', 'expired')->orWhere('ends_at', '<=', now())->count(),
        ];
        $recentRestaurants = Restaurant::latest()->limit(5)->get();
        $recentOrders = Order::with('restaurant')->latest()->limit(5)->get();
        return view('panel.dashboard.admin', compact('stats', 'recentRestaurants', 'recentOrders'));
    }

    public function vendorDashboard(Request $request): View
    {
        $restaurant = $request->user()->restaurant;
        abort_if(!$restaurant, 404);
        $rid = $restaurant->id;
        $stats = [
            'today_orders' => Order::where('restaurant_id', $rid)->whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('restaurant_id', $rid)->where('status', 'pending')->count(),
            'preparing_orders' => Order::where('restaurant_id', $rid)->where('status', 'preparing')->count(),
            'completed_orders' => Order::where('restaurant_id', $rid)->where('status', 'delivered')->count(),
            'today_revenue' => Order::where('restaurant_id', $rid)->whereDate('created_at', today())->sum('total'),
            'monthly_revenue' => Order::where('restaurant_id', $rid)->whereMonth('created_at', now()->month)->sum('total'),
            'total_customers' => Customer::where('restaurant_id', $rid)->count(),
            'total_products' => Product::where('restaurant_id', $rid)->count(),
        ];
        $recentOrders = Order::where('restaurant_id', $rid)->latest()->limit(5)->get();
        return view('panel.dashboard.vendor', compact('stats', 'restaurant', 'recentOrders'));
    }

    public function staffDashboard(Request $request): View
    {
        return $this->vendorDashboard($request);
    }

    // Admin helpers
    public function restaurants(): View { $restaurants = Restaurant::with('owner')->latest()->paginate(20); return view('panel.resources.index', ['title'=>'Restaurants','items'=>$restaurants,'type'=>'restaurants']); }
    public function staff(): View { $staff = User::where('role','staff')->with('restaurant')->paginate(20); return view('panel.resources.index', ['title'=>'Staff','items'=>$staff,'type'=>'staff']); }
    public function customers(): View { $customers = Customer::with('restaurant')->latest()->paginate(20); return view('panel.resources.index', ['title'=>'Customers','items'=>$customers,'type'=>'customers']); }
    public function orders(): View { $orders = Order::with('restaurant')->latest()->paginate(20); return view('panel.orders.index', compact('orders')); }
    public function products(): View { $products = Product::with('restaurant','category')->latest()->paginate(20); return view('panel.resources.index', ['title'=>'Products','items'=>$products,'type'=>'products']); }
    public function categories(): View { $cats = Category::with('restaurant')->latest()->paginate(20); return view('panel.resources.index', ['title'=>'Categories','items'=>$cats,'type'=>'categories']); }
    public function subscriptions(): View { $subs = Subscription::with('restaurant','plan')->latest()->paginate(20); return view('panel.subscriptions.admin', compact('subs')); }
    public function payments(): View { $payments = DB::table('payments')->latest()->paginate(20); return view('panel.resources.index', ['title'=>'Payments','items'=>$payments,'type'=>'payments']); }
    public function whatsapp(Request $request): View {
        $restaurant = $request->user()->restaurant;
        $settings = null;
        if ($request->user()->hasRole('super_admin')) {
            $settings = WhatsAppSetting::whereNull('restaurant_id')->first();
        } elseif ($restaurant) {
            $settings = WhatsAppSetting::where('restaurant_id', $restaurant->id)->first();
        }
        return view('panel.operations.whatsapp', compact('settings'));
    }
    public function updateWhatsapp(Request $request): RedirectResponse {
        $data = $request->validate([
            'phone_number' => ['nullable','string','max:30'],
            'phone_number_id' => ['nullable','string','max:100'],
            'business_account_id' => ['nullable','string','max:100'],
            'access_token' => ['nullable','string','max:2000'],
            'webhook_verify_token' => ['nullable','string','max:255'],
            'app_secret' => ['nullable','string','max:255'],
            'api_version' => ['nullable','string','max:20'],
            'is_enabled' => ['nullable','boolean'],
        ]);
        $restaurant = $request->user()->restaurant;
        $restaurantId = $request->user()->hasRole('super_admin') ? null : $restaurant?->id;
        // Keep existing token if masked
        $existing = WhatsAppSetting::where('restaurant_id', $restaurantId)->first();
        if (!empty($data['access_token']) && str_contains($data['access_token'], '•')) $data['access_token'] = $existing?->access_token;
        $data['is_enabled'] = $request->boolean('is_enabled');
        WhatsAppSetting::updateOrCreate(['restaurant_id'=>$restaurantId], $data);
        return back()->with('success','WhatsApp settings saved. Restaurant settings override global config.');
    }
    public function reports(): View { return view('panel.operations.reports'); }
    public function activityLogs(): View { $logs = DB::table('activity_logs')->latest()->paginate(20); return view('panel.resources.index', ['title'=>'Activity Logs','items'=>$logs,'type'=>'activity_logs']); }
    public function websiteSettings(): View { return view('panel.settings.edit', ['section'=>'website']); }
    public function systemSettings(): View { return view('panel.settings.edit', ['section'=>'system']); }

    // Restaurant status actions
    public function approve(Restaurant $restaurant): RedirectResponse { $restaurant->update(['status'=>'active']); return back()->with('success','Restaurant approved.'); }
    public function reject(Restaurant $restaurant): RedirectResponse { $restaurant->update(['status'=>'rejected']); return back()->with('success','Restaurant rejected.'); }
    public function activate(Restaurant $restaurant): RedirectResponse { $restaurant->update(['status'=>'active']); return back()->with('success','Restaurant activated.'); }
    public function deactivate(Restaurant $restaurant): RedirectResponse { $restaurant->update(['status'=>'suspended']); return back()->with('success','Restaurant suspended.'); }

    public function restaurantProfile(Request $request): View { $restaurant = $request->user()->restaurant; return view('panel.settings.edit', compact('restaurant')); }
    public function updateProfile(Request $request): RedirectResponse {
        $restaurant = $request->user()->restaurant;
        $data = $request->validate(['name'=>'required|string|max:255','description'=>'nullable|string','phone'=>'nullable|string','email'=>'nullable|email','address'=>'nullable|string','city'=>'nullable|string','state'=>'nullable|string','country'=>'nullable|string','postal_code'=>'nullable|string','facebook'=>'nullable|url','instagram'=>'nullable|url','youtube'=>'nullable|url','website'=>'nullable|url']);
        $restaurant->update($data);
        return back()->with('success','Profile updated.');
    }
    public function businessHours(Request $request): View { $restaurant = $request->user()->restaurant->load('businessHours'); return view('panel.settings.business-hours', compact('restaurant')); }
    public function updateBusinessHours(Request $request): RedirectResponse {
        $restaurant = $request->user()->restaurant;
        $data = $request->validate(['hours'=>'required|array','hours.*.is_closed'=>'boolean','hours.*.opens_at'=>'nullable|date_format:H:i','hours.*.closes_at'=>'nullable|date_format:H:i']);
        foreach ($data['hours'] as $day => $h) { $restaurant->businessHours()->updateOrCreate(['day'=>$day], $h); }
        return back()->with('success','Business hours updated.');
    }
    public function qrCode(Request $request): View { $restaurant = $request->user()->restaurant; return view('panel.operations.qr', compact('restaurant')); }
    public function staffCustomers(Request $request): View { $rid = $request->user()->restaurant_id; $customers = Customer::where('restaurant_id',$rid)->paginate(20); return view('panel.resources.index', ['title'=>'Customers','items'=>$customers,'type'=>'customers']); }
}