<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductAddonItem;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PanelResourceController extends Controller
{
    // Categories
    public function categories(Request $request): View {
        $cats = Category::where('restaurant_id', $request->user()->restaurant_id)->orderBy('sort_order')->paginate(20);
        return view('panel.resources.index', ['title'=>'Categories','items'=>$cats,'type'=>'categories']);
    }
    public function storeCategory(Request $request): RedirectResponse {
        $data = $request->validate(['name'=>'required|string|max:255','description'=>'nullable|string','is_active'=>'boolean','sort_order'=>'nullable|integer']);
        $data['restaurant_id']=$request->user()->restaurant_id;
        $data['slug']=Str::slug($data['name']).'-'.Str::random(6);
        $data['is_active']=$request->boolean('is_active', true);
        Category::create($data);
        return back()->with('success','Category created.');
    }
    public function updateCategory(Request $request, Category $category): RedirectResponse {
        $this->authorize('update', $category);
        $data = $request->validate(['name'=>'required|string|max:255','description'=>'nullable|string','is_active'=>'boolean','sort_order'=>'nullable|integer']);
        $data['is_active']=$request->boolean('is_active', true);
        $category->update($data);
        return back()->with('success','Category updated.');
    }
    public function deleteCategory(Request $request, Category $category): RedirectResponse {
        $this->authorize('delete', $category);
        $category->delete();
        return back()->with('success','Category deleted.');
    }

    // Products
    public function products(Request $request): View {
        $products = Product::where('restaurant_id', $request->user()->restaurant_id)->with('category')->latest()->paginate(20);
        $categories = Category::where('restaurant_id', $request->user()->restaurant_id)->get();
        return view('panel.resources.index', ['title'=>'Products','items'=>$products,'type'=>'products','categories'=>$categories]);
    }
    public function storeProduct(Request $request): RedirectResponse {
        $data = $request->validate([
            'name'=>'required|string|max:255','category_id'=>'required|exists:categories,id','description'=>'nullable|string','price'=>'required|integer|min:0','discount_price'=>'nullable|integer|min:0','in_stock'=>'boolean','is_featured'=>'boolean','is_popular'=>'boolean','is_available'=>'boolean',
        ]);
        $data['restaurant_id']=$request->user()->restaurant_id;
        $data['slug']=Str::slug($data['name']).'-'.Str::random(6);
        foreach(['in_stock','is_featured','is_popular','is_available'] as $f) $data[$f]=$request->boolean($f, true);
        Product::create($data);
        return back()->with('success','Product created.');
    }
    public function updateProduct(Request $request, Product $product): RedirectResponse {
        $this->authorize('update', $product);
        $data = $request->validate(['name'=>'required|string|max:255','category_id'=>'required|exists:categories,id','description'=>'nullable|string','price'=>'required|integer|min:0','discount_price'=>'nullable|integer|min:0']);
        $product->update($data);
        return back()->with('success','Product updated.');
    }
    public function deleteProduct(Request $request, Product $product): RedirectResponse {
        $this->authorize('delete', $product);
        $product->delete();
        return back()->with('success','Product deleted.');
    }

    // Variants
    public function variants(Request $request): View {
        $variants = ProductVariant::where('restaurant_id', $request->user()->restaurant_id)->with('product')->paginate(20);
        $products = Product::where('restaurant_id', $request->user()->restaurant_id)->get();
        return view('panel.resources.index', ['title'=>'Variants','items'=>$variants,'type'=>'variants','products'=>$products]);
    }
    public function storeVariant(Request $request): RedirectResponse {
        $data = $request->validate(['product_id'=>'required|exists:products,id','name'=>'required|string|max:255','price'=>'required|integer|min:0']);
        $data['restaurant_id']=$request->user()->restaurant_id;
        ProductVariant::create($data);
        return back()->with('success','Variant created.');
    }

    // Addons
    public function addons(Request $request): View {
        $addons = ProductAddon::where('restaurant_id', $request->user()->restaurant_id)->with('product','items')->paginate(20);
        $products = Product::where('restaurant_id', $request->user()->restaurant_id)->get();
        return view('panel.resources.index', ['title'=>'Add-ons','items'=>$addons,'type'=>'addons','products'=>$products]);
    }
    public function storeAddon(Request $request): RedirectResponse {
        $data = $request->validate(['product_id'=>'required|exists:products,id','name'=>'required|string|max:255','is_required'=>'boolean','max_selections'=>'nullable|integer']);
        $data['restaurant_id']=$request->user()->restaurant_id;
        $data['is_required']=$request->boolean('is_required');
        $addon = ProductAddon::create($data);
        if($request->filled('addon_items')) {
            foreach($request->input('addon_items') as $item) {
                ProductAddonItem::create(['restaurant_id'=>$data['restaurant_id'],'product_addon_id'=>$addon->id,'name'=>$item['name'],'price'=>$item['price'] ?? 0]);
            }
        }
        return back()->with('success','Add-on created.');
    }

    // Coupons
    public function coupons(Request $request): View {
        $coupons = Coupon::where('restaurant_id', $request->user()->restaurant_id)->latest()->paginate(20);
        return view('panel.resources.index', ['title'=>'Coupons','items'=>$coupons,'type'=>'coupons']);
    }
    public function storeCoupon(Request $request): RedirectResponse {
        $data = $request->validate(['code'=>'required|string|max:50','type'=>'required|in:fixed,percentage','value'=>'required|numeric|min:0','min_order'=>'nullable|integer','max_discount'=>'nullable|integer','starts_at'=>'nullable|date','ends_at'=>'nullable|date','usage_limit'=>'nullable|integer','per_customer_limit'=>'nullable|integer']);
        $data['restaurant_id']=$request->user()->restaurant_id;
        $data['code']=strtoupper($data['code']);
        Coupon::create($data);
        return back()->with('success','Coupon created.');
    }
    public function updateCoupon(Request $request, Coupon $coupon): RedirectResponse {
        $this->authorize('update', $coupon);
        $data = $request->validate(['code'=>'required|string|max:50','type'=>'required|in:fixed,percentage','value'=>'required|numeric|min:0']);
        $coupon->update($data);
        return back()->with('success','Coupon updated.');
    }
}