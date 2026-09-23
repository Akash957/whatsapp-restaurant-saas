@extends('layouts.panel')
@section('page_title','Settings')
@section('content')
<div class="card"><div class="card-header fw-semibold">Restaurant Settings</div><div class="card-body">
@if(!isset($restaurant))
<p class="text-muted">System settings — configure globally via .env and Settings table.</p>
<form><div class="mb-3"><label class="form-label">App Name</label><input class="form-control" value="{{ config('app.name') }}" disabled></div><p class="small text-muted">Edit .env to change system settings.</p></form>
@else
<form method="POST" action="{{ route('vendor.settings.update') }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Name</label><input name="name" value="{{ old('name',$restaurant->name) }}" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Email</label><input name="email" value="{{ old('email',$restaurant->email) }}" class="form-control"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ old('description',$restaurant->description) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Phone</label><input name="phone" value="{{ old('phone',$restaurant->phone) }}" class="form-control"></div>
<div class="col-md-6"><label class="form-label">WhatsApp</label><input name="whatsapp_number" value="{{ old('whatsapp_number',$restaurant->whatsapp_number) }}" class="form-control"></div>
<div class="col-12"><label class="form-label">Address</label><input name="address" value="{{ old('address',$restaurant->address) }}" class="form-control"></div>
<div class="col-md-4"><label class="form-label">City</label><input name="city" value="{{ old('city',$restaurant->city) }}" class="form-control"></div>
<div class="col-md-4"><label class="form-label">State</label><input name="state" value="{{ old('state',$restaurant->state) }}" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Postal Code</label><input name="postal_code" value="{{ old('postal_code',$restaurant->postal_code) }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Primary Color</label><input type="color" name="primary_color" value="{{ old('primary_color',$restaurant->primary_color) }}" class="form-control form-control-color"></div>
<div class="col-md-3"><label class="form-label">Facebook</label><input name="facebook" value="{{ old('facebook',$restaurant->facebook) }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Instagram</label><input name="instagram" value="{{ old('instagram',$restaurant->instagram) }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Website</label><input name="website" value="{{ old('website',$restaurant->website) }}" class="form-control"></div>
<div class="col-12"><label class="form-label">Custom Footer</label><textarea name="custom_footer" class="form-control" rows="2">{{ old('custom_footer',$restaurant->custom_footer) }}</textarea></div>
<hr class="my-2"><h6>Delivery Settings</h6>
@php $ds=$restaurant->deliverySetting; @endphp
<div class="col-md-3"><label class="form-check"><input type="checkbox" name="enable_delivery" value="1" @checked($ds->enable_delivery ?? true) class="form-check-input"> Delivery</label></div>
<div class="col-md-3"><label class="form-check"><input type="checkbox" name="enable_pickup" value="1" @checked($ds->enable_pickup ?? true) class="form-check-input"> Pickup</label></div>
<div class="col-md-3"><label class="form-check"><input type="checkbox" name="enable_dine_in" value="1" @checked($ds->enable_dine_in ?? true) class="form-check-input"> Dine-in</label></div>
<div class="col-md-3"><label class="form-label">Tax %</label><input name="tax_rate" value="{{ old('tax_rate',$ds->tax_rate ?? 5) }}" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Min Order (paise)</label><input type="number" name="minimum_order" value="{{ old('minimum_order',$ds->minimum_order ?? 0) }}" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Delivery Charge</label><input type="number" name="delivery_charge" value="{{ old('delivery_charge',$ds->delivery_charge ?? 0) }}" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Free Delivery Above</label><input type="number" name="free_delivery_above" value="{{ old('free_delivery_above',$ds->free_delivery_above) }}" class="form-control"></div>
</div>
<button class="btn btn-primary mt-3">Save Settings</button>
</form>
@endif
</div></div>
@endsection