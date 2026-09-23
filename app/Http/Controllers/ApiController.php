<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private function ok(mixed $data=null, string $msg='Operation successful'): JsonResponse { return response()->json(['success'=>true,'message'=>$msg,'data'=>$data]); }
    private function fail(string $msg='Validation failed', mixed $errors=[], int $code=422): JsonResponse { return response()->json(['success'=>false,'message'=>$msg,'errors'=>$errors], $code); }

    public function login(Request $request): JsonResponse {
        $data = $request->validate(['email'=>'required|email','password'=>'required']);
        if(!auth()->attempt($data)) return $this->fail('Invalid credentials',[],401);
        $user = $request->user();
        $token = $user->createToken('api')->plainTextToken;
        return $this->ok(['token'=>$token,'user'=>$user]);
    }
    public function register(Request $request): JsonResponse { return $this->fail('Use /register endpoint',[],400); }
    public function restaurants(): JsonResponse { return $this->ok(Restaurant::where('status','active')->paginate(20)); }
    public function showRestaurant(Restaurant $restaurant): JsonResponse { return $this->ok($restaurant); }
    public function categories(Request $request): JsonResponse { return $this->ok(Category::where('is_active',true)->paginate(20)); }
    public function categoryProducts(Request $request, Category $category): JsonResponse { return $this->ok($category->products()->where('is_available',true)->paginate(20)); }
    public function products(Request $request): JsonResponse { return $this->ok(Product::where('is_available',true)->with('category')->paginate(20)); }
    public function productDetail(Product $product): JsonResponse { return $this->ok($product->load('variants','addons.items')); }
    public function cartAdd(): JsonResponse { return $this->ok([],'Cart via web session'); }
    public function cartRemove(): JsonResponse { return $this->ok(); }
    public function coupon(): JsonResponse { return $this->ok(); }
    public function checkout(): JsonResponse { return $this->ok(); }
    public function orderDetail(Order $order): JsonResponse { return $this->ok($order->load('items')); }
    public function payments(): JsonResponse { return $this->ok([]); }
    public function whatsapp(): JsonResponse { return $this->ok(); }
    public function subscriptions(): JsonResponse { return $this->ok([]); }
    public function webhookVerify(Request $request): JsonResponse {
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');
        if($token === config('services.whatsapp.webhook_verify_token') || $token === env('WHATSAPP_WEBHOOK_VERIFY_TOKEN')) return response($challenge)->header('Content-Type','text/plain');
        return response()->json(['error'=>'Verification failed'],403);
    }
    public function webhookReceive(Request $request): JsonResponse {
        \App\Models\WhatsAppWebhook::create(['event_id'=> (string) \Illuminate\Support\Str::uuid(),'payload'=>$request->all(),'status'=>'pending']);
        return $this->ok([],'Webhook received');
    }
    public function razorpayWebhook(Request $request): JsonResponse { return $this->ok([],'Razorpay webhook received'); }
    public function templates(): JsonResponse { return $this->ok(\App\Models\WhatsAppTemplate::paginate(20)); }
    public function storeTemplate(Request $request): JsonResponse { $data=$request->validate(['name'=>'required|string','body'=>'required|string','event'=>'nullable|string']); $t=\App\Models\WhatsAppTemplate::create($data); return $this->ok($t,'Template created'); }
    public function automations(): JsonResponse { return $this->ok(\App\Models\WhatsAppAutomation::paginate(20)); }
    public function storeAutomation(Request $request): JsonResponse { $data=$request->validate(['name'=>'required|string','event'=>'required|string','whatsapp_template_id'=>'required|exists:whatsapp_templates,id']); $a=\App\Models\WhatsAppAutomation::create($data); return $this->ok($a,'Automation created'); }
    public function conversations(): JsonResponse { return $this->ok(\App\Models\WhatsAppConversation::paginate(20)); }
    public function startConversation(): JsonResponse { return $this->ok(); }
    // Aliases for earlier route names
    public function index(){ return $this->restaurants();}
    public function show(Restaurant $r){ return $this->showRestaurant($r);}
    public function indexRestaurants(){ return $this->restaurants();}
}