<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DeliverySetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductAddonItem;
use App\Models\ProductVariant;
use App\Models\Restaurant;
use App\Models\RestaurantBusinessHour;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\WhatsAppTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['email'=>'admin@example.com'],[
            'name'=>'Super Admin','password'=>Hash::make('password'),'role'=>'super_admin','is_active'=>true,'email_verified_at'=>now(),
        ]);
        $vendor = User::firstOrCreate(['email'=>'vendor@example.com'],[
            'name'=>'Green Food Owner','phone'=>'+919876543210','password'=>Hash::make('password'),'role'=>'vendor','is_active'=>true,'email_verified_at'=>now(),
        ]);

        $restaurant = Restaurant::firstOrCreate(['slug'=>'green-food-restaurant'],[
            'owner_id'=>$vendor->id,'name'=>'Green Food Restaurant','status'=>'active','description'=>'Authentic Indian & Chinese cuisine','email'=>'contact@greenfood.test','phone'=>'+919876543210','whatsapp_number'=>'+919876543210','address'=>'123 MG Road','city'=>'Bengaluru','state'=>'Karnataka','country'=>'India','postal_code'=>'560001','latitude'=>12.9716,'longitude'=>77.5946,
        ]);
        $vendor->update(['restaurant_id'=>$restaurant->id]);

        if($restaurant->deliverySetting()->count()===0){
            DeliverySetting::create(['restaurant_id'=>$restaurant->id,'enable_delivery'=>true,'enable_pickup'=>true,'enable_dine_in'=>true,'minimum_order'=>0,'delivery_charge'=>4000,'free_delivery_above'=>50000,'tax_rate'=>5]);
        }
        if($restaurant->businessHours()->count()===0){
            foreach(range(0,6) as $d) RestaurantBusinessHour::create(['restaurant_id'=>$restaurant->id,'day'=>$d,'is_closed'=>false,'opens_at'=>'10:00:00','closes_at'=>'22:00:00']);
        }
        $plan = SubscriptionPlan::where('slug','professional')->first();
        if($plan && $restaurant->subscriptions()->count()===0){
            Subscription::create(['restaurant_id'=>$restaurant->id,'subscription_plan_id'=>$plan->id,'status'=>'active','starts_at'=>now(),'ends_at'=>now()->addDays(30)]);
        }

        $cats = [];
        foreach(['Burgers','Pizza','Biryani','Chinese','Drinks','Desserts'] as $i=>$name){
            $cats[$name]=Category::firstOrCreate(['restaurant_id'=>$restaurant->id,'slug'=>Str::slug($name)],['name'=>$name,'is_active'=>true,'sort_order'=>$i]);
        }

        $products = [
            ['name'=>'Veg Burger','cat'=>'Burgers','price'=>9900,'desc'=>'Crispy veg patty with fresh veggies'],
            ['name'=>'Chicken Burger','cat'=>'Burgers','price'=>14900,'desc'=>'Juicy chicken patty'],
            ['name'=>'Margherita Pizza','cat'=>'Pizza','price'=>19900,'desc'=>'Classic cheese & tomato'],
            ['name'=>'Paneer Biryani','cat'=>'Biryani','price'=>24900,'desc'=>'Aromatic basmati with paneer'],
            ['name'=>'Chicken Biryani','cat'=>'Biryani','price'=>29900,'desc'=>'Hyderabadi style'],
            ['name'=>'Hakka Noodles','cat'=>'Chinese','price'=>17900,'desc'=>'Stir-fried noodles with veggies'],
            ['name'=>'Cold Coffee','cat'=>'Drinks','price'=>9900,'desc'=>'Creamy cold coffee'],
            ['name'=>'Gulab Jamun','cat'=>'Desserts','price'=>8900,'desc'=>'Soft milk balls in syrup'],
        ];
        foreach($products as $pd){
            $p = Product::firstOrCreate(['restaurant_id'=>$restaurant->id,'slug'=>Str::slug($pd['name'])],[
                'category_id'=>$cats[$pd['cat']]->id,'name'=>$pd['name'],'description'=>$pd['desc'],'short_description'=>$pd['desc'],'price'=>$pd['price'],'in_stock'=>true,'is_featured'=>rand(0,1),'is_popular'=>rand(0,1),'is_available'=>true,'sort_order'=>rand(1,100),
            ]);
            if($p->variants()->count()===0 && in_array($pd['cat'],['Burgers','Pizza','Drinks'])){
                foreach(['Small'=>-2000,'Medium'=>0,'Large'=>5000] as $vn=>$delta){
                    ProductVariant::create(['restaurant_id'=>$restaurant->id,'product_id'=>$p->id,'name'=>$vn,'price'=>max(1000,$p->price+$delta),'is_available'=>true]);
                }
            }
            if($p->addons()->count()===0 && $pd['cat']==='Pizza'){
                $addon=ProductAddon::create(['restaurant_id'=>$restaurant->id,'product_id'=>$p->id,'name'=>'Extra Toppings','is_required'=>false,'max_selections'=>3]);
                foreach([['Extra Cheese',3000],['Extra Sauce',1000],['Extra Toppings',2000]] as [$n,$pr]) ProductAddonItem::create(['restaurant_id'=>$restaurant->id,'product_addon_id'=>$addon->id,'name'=>$n,'price'=>$pr]);
            }
        }

        $customerUser = User::firstOrCreate(['email'=>'customer@example.com'],[
            'name'=>'Demo Customer','phone'=>'+919876543211','password'=>Hash::make('password'),'role'=>'customer','is_active'=>true,'email_verified_at'=>now(),
        ]);
        $customer = Customer::firstOrCreate(['restaurant_id'=>$restaurant->id,'mobile'=>'+919876543211'],[
            'user_id'=>$customerUser->id,'name'=>'Demo Customer','email'=>'customer@example.com','is_active'=>true,
        ]);

        WhatsAppTemplate::firstOrCreate(['restaurant_id'=>null,'name'=>'order_confirmation'],[
            'event'=>'order.created','title'=>'Order Confirmed','body'=>"Thank you for your order!\n\nOrder No: {order_no}\nCustomer: {customer_name}\n\nItems:\n{item_name}\nSubtotal: {sub_total}\nDelivery: {delivery_charge}\nTotal: {grand_total}\n\nTrack Order:\n{track_order_url}",'is_enabled'=>true,
        ]);
        foreach(['order_accepted','order_preparing','out_for_delivery','order_delivered'] as $ev){
            WhatsAppTemplate::firstOrCreate(['restaurant_id'=>null,'name'=>$ev],[
                'event'=>$ev,'title'=>Str::headline($ev),'body'=>"Hi {customer_name}, your order {order_no} is now ".str_replace('_',' ',$ev).". Total: {grand_total}. Track: {track_order_url}",'is_enabled'=>true,
            ]);
        }

        // Demo orders if none
        if(Order::where('restaurant_id',$restaurant->id)->count()===0){
            // Create via service would be ideal but create directly for seeding
            $prod = Product::where('restaurant_id',$restaurant->id)->first();
            $o = Order::create([
                'restaurant_id'=>$restaurant->id,'customer_id'=>$customer->id,'user_id'=>$customerUser->id,
                'order_number'=>'ORD-'.now()->year.'-000001','tracking_token'=>Str::uuid(),'status'=>'delivered','payment_method'=>'cod','payment_status'=>'paid','order_type'=>'delivery',
                'customer_name'=>$customer->name,'customer_mobile'=>$customer->mobile,'customer_email'=>$customer->email,'address'=>'123 MG Road','postal_code'=>'560001','notes'=>'Demo order',
                'subtotal'=>19900,'discount'=>0,'tax'=>995,'delivery_charge'=>4000,'tips'=>0,'total'=>24895,'idempotency_key'=>Str::uuid(),
            ]);
            if($prod){ \App\Models\OrderItem::create(['restaurant_id'=>$restaurant->id,'order_id'=>$o->id,'product_id'=>$prod->id,'name'=>$prod->name,'quantity'=>1,'unit_price'=>$prod->price,'total'=>$prod->price]); }
            \App\Models\OrderStatusHistory::create(['restaurant_id'=>$restaurant->id,'order_id'=>$o->id,'status'=>'pending','notes'=>'Order placed']);
            \App\Models\OrderStatusHistory::create(['restaurant_id'=>$restaurant->id,'order_id'=>$o->id,'status'=>'delivered','notes'=>'Demo delivered']);
        }
    }
}