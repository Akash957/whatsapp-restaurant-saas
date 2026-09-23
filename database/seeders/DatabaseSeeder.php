<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name'=>'super_admin','label'=>'Super Admin'],
            ['name'=>'vendor','label'=>'Vendor'],
            ['name'=>'staff','label'=>'Staff'],
            ['name'=>'customer','label'=>'Customer'],
        ];
        foreach($roles as $r) Role::firstOrCreate(['name'=>$r['name']], $r);

        $perms = ['manage_catalog','manage_orders','manage_customers','manage_settings','manage_whatsapp','manage_reports'];
        foreach($perms as $p) Permission::firstOrCreate(['name'=>$p]);
        $staffRole = Role::where('name','staff')->first();
        if($staffRole) $staffRole->permissions()->syncWithoutDetaching(Permission::whereIn('name',['manage_orders','manage_customers'])->pluck('id'));

        $plans = [
            ['name'=>'Basic','slug'=>'basic','price'=>19900,'duration_days'=>30,'description'=>'For small restaurants starting online','max_products'=>50,'max_orders'=>500,'max_staff'=>2,'whatsapp_messages'=>500,'ai_automation'=>false,'custom_domain'=>false,'white_label'=>false,'reports'=>true,'support'=>'Email'],
            ['name'=>'Professional','slug'=>'professional','price'=>49900,'duration_days'=>30,'description'=>'For growing restaurants','max_products'=>200,'max_orders'=>2000,'max_staff'=>5,'whatsapp_messages'=>2000,'ai_automation'=>true,'custom_domain'=>true,'white_label'=>false,'reports'=>true,'support'=>'Priority Email'],
            ['name'=>'Enterprise','slug'=>'enterprise','price'=>99900,'duration_days'=>30,'description'=>'For large chains','max_products'=>1000,'max_orders'=>10000,'max_staff'=>20,'whatsapp_messages'=>10000,'ai_automation'=>true,'custom_domain'=>true,'white_label'=>true,'reports'=>true,'support'=>'24/7 Phone'],
        ];
        foreach($plans as $pl) SubscriptionPlan::firstOrCreate(['slug'=>$pl['slug']], $pl);

        $this->call(DemoSeeder::class);
    }
}