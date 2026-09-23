@extends('layouts.panel')
@section('page_title', $title ?? 'Resources')
@section('content')

{{-- Header --}}
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-end mb-3">
    <div>
        <h4 class="brand-font mb-1" style="font-weight:700; letter-spacing:-.02em">{{ $title ?? 'Resources' }}</h4>
        <div class="text-muted small">
            @if(($type ?? '')==='restaurants') Manage every restaurant — approve, suspend, and monitor.
            @elseif(($type ?? '')==='products') Your menu — search, filter, and manage availability.
            @elseif(($type ?? '')==='categories') Organize your menu for discovery.
            @else Manage and search all records. @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <div class="input-group" style="max-width:280px">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input id="tableSearch" class="form-control border-start-0" placeholder="Search..." oninput="filterRows(this.value)">
        </div>
    </div>
</div>

{{-- Inline create forms --}}
@if(($type ?? '')==='categories')
<div class="card mb-3" style="border-radius:16px"><div class="card-body">
    <div class="small fw-semibold text-muted mb-2" style="letter-spacing:.06em; text-transform:uppercase">New Category</div>
    <form method="POST" action="{{ route('vendor.categories.store') }}" class="row g-2 align-items-end">@csrf
        <div class="col-md-4"><label class="form-label small fw-semibold">Name</label><input name="name" class="form-control" placeholder="e.g. Burgers" required></div>
        <div class="col-md-5"><label class="form-label small fw-semibold">Description</label><input name="description" class="form-control" placeholder="Short description"></div>
        <div class="col-md-1"><label class="form-label small fw-semibold">Order</label><input name="sort_order" type="number" class="form-control" value="0"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Add</button></div>
    </form>
</div></div>
@endif

@if(($type ?? '')==='products')
<div class="card mb-3" style="border-radius:16px"><div class="card-body">
    <div class="small fw-semibold text-muted mb-2" style="letter-spacing:.06em; text-transform:uppercase">New Product</div>
    <form method="POST" action="{{ route('vendor.products.store') }}" class="row g-2 align-items-end">@csrf
        <div class="col-md-4"><label class="form-label small fw-semibold">Product name</label><input name="name" class="form-control" placeholder="e.g. Margherita Pizza" required></div>
        <div class="col-md-3"><label class="form-label small fw-semibold">Category</label><select name="category_id" class="form-select" required><option value="">Select</option>@foreach($categories ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label small fw-semibold">Price (paise)</label><input name="price" type="number" class="form-control" placeholder="19900" required></div>
        <div class="col-md-1"><label class="form-label small fw-semibold">Discount</label><input name="discount_price" type="number" class="form-control" placeholder="14900"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Add</button></div>
    </form>
</div></div>
@endif

@if(($type ?? '')==='coupons')
<div class="card mb-3" style="border-radius:16px"><div class="card-body">
    <div class="small fw-semibold text-muted mb-2" style="letter-spacing:.06em; text-transform:uppercase">New Coupon</div>
    <form method="POST" action="{{ route('vendor.coupons.store') }}" class="row g-2 align-items-end">@csrf
        <div class="col-md-2"><label class="form-label small fw-semibold">Code</label><input name="code" class="form-control text-uppercase" placeholder="WELCOME10" required></div>
        <div class="col-md-2"><label class="form-label small fw-semibold">Type</label><select name="type" class="form-select"><option value="fixed">Fixed</option><option value="percentage">Percentage</option></select></div>
        <div class="col-md-2"><label class="form-label small fw-semibold">Value</label><input name="value" class="form-control" placeholder="10 or 1000" required></div>
        <div class="col-md-2"><label class="form-label small fw-semibold">Min order</label><input name="min_order" type="number" class="form-control" placeholder="0"></div>
        <div class="col-md-2"><label class="form-label small fw-semibold">Limit</label><input name="usage_limit" type="number" class="form-control" placeholder="100"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Create</button></div>
    </form>
</div></div>
@endif

@if(($type ?? '')==='variants')
<div class="card mb-3" style="border-radius:16px"><div class="card-body">
    <form method="POST" action="{{ route('vendor.variants.store') }}" class="row g-2 align-items-end">@csrf
        <div class="col-md-5"><label class="form-label small fw-semibold">Product</label><select name="product_id" class="form-select" required><option value="">Select product</option>@foreach($products ?? [] as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label small fw-semibold">Variant</label><input name="name" class="form-control" placeholder="Large" required></div>
        <div class="col-md-2"><label class="form-label small fw-semibold">Price</label><input name="price" type="number" class="form-control" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
    </form>
</div></div>
@endif

{{-- Content --}}
@if(($type ?? '')==='restaurants')
    <div class="card" style="border-radius:16px; overflow:hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="dataTable">
                    <thead style="background:#f8faf9; border-bottom:1px solid #e8efec">
                        <tr class="small text-muted" style="letter-spacing:.04em; text-transform:uppercase">
                            <th class="ps-4 py-3" style="width:42%">Restaurant</th>
                            <th class="py-3">Owner & Contact</th>
                            <th class="py-3">Location</th>
                            <th class="py-3">Status</th>
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items ?? [] as $r)
                        @php $owner = $r->owner; @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex gap-3 align-items-center">
                                    <span class="d-inline-grid place-items-center rounded-3 flex-shrink-0" style="width:44px;height:44px; display:grid; background:var(--primary-50); color:var(--primary); font-weight:800">{{ strtoupper(substr($r->name,0,1)) }}</span>
                                    <span style="min-width:0">
                                        <span class="d-block fw-semibold" style="line-height:1.2">{{ $r->name }}</span>
                                        <span class="small text-muted" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; max-width:260px">/{{ $r->slug }} · #{{ $r->id }}</span>
                                        @if($r->description)<span class="small text-muted d-none d-xl-block" style="display:block; max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $r->description }}</span>@endif
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="d-block fw-semibold">{{ $owner->name ?? '—' }}</span>
                                    <span class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $r->email }}</span><br>
                                    <span class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $r->phone }}</span>
                                </div>
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $r->city }}, {{ $r->state }}<br>
                                <span class="small">{{ $r->postal_code }} · {{ $r->country }}</span>
                            </td>
                            <td>
                                @if($r->status==='active')<span class="badge badge-soft" style="background:#dcfce7; color:#166534"><i class="bi bi-check-circle me-1"></i>Active</span>
                                @elseif($r->status==='pending')<span class="badge badge-soft" style="background:#fef9c3; color:#854d0e"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @elseif($r->status==='rejected')<span class="badge badge-soft" style="background:#fee2e2; color:#991b1b">Rejected</span>
                                @elseif($r->status==='suspended')<span class="badge badge-soft" style="background:#fee2e2; color:#991b1b">Suspended</span>
                                @else<span class="badge bg-light text-dark border text-capitalize">{{ $r->status }}</span>@endif
                                <div class="small text-muted mt-1">{{ $r->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                    @if($r->status==='pending')
                                        <a href="{{ route('admin.restaurant.approve',$r) }}" class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i> Approve</a>
                                        <a href="{{ route('admin.restaurant.reject',$r) }}" class="btn btn-sm btn-outline-danger" title="Reject"><i class="bi bi-x-lg"></i></a>
                                    @endif
                                    @if($r->status==='active')
                                        <a href="{{ route('admin.restaurant.deactivate',$r) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pause-circle me-1"></i> Suspend</a>
                                    @endif
                                    @if(in_array($r->status,['suspended','rejected']))
                                        <a href="{{ route('admin.restaurant.activate',$r) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-play-circle me-1"></i> Activate</a>
                                    @endif
                                    <a href="{{ url('/restaurant/'.$r->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View store"><i class="bi bi-box-arrow-up-right"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5"><i class="bi bi-shop fs-1 text-muted d-block mb-2"></i><span class="text-muted">No restaurants yet — new registrations will appear here.</span></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($items) && method_exists($items,'links'))<div class="card-footer bg-white border-top d-flex justify-content-between align-items-center"><span class="small text-muted">Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }}</span><span>{{ $items->links('pagination::bootstrap-5') }}</span></div>@endif
    </div>
@elseif(in_array($type ?? '', ['products','categories','variants','coupons']))
    <div class="card" style="border-radius:16px; overflow:hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="dataTable">
                    <thead style="background:#f8faf9; border-bottom:1px solid #e8efec">
                        <tr class="small text-muted" style="letter-spacing:.04em; text-transform:uppercase">
                            <th class="ps-4 py-3" style="width:46%">Item</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Price</th>
                            <th class="py-3">Status</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items ?? [] as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex gap-3 align-items-center">
                                    <span class="rounded-3 flex-shrink-0" style="width:42px;height:42px; display:grid; place-items:center; background:#f1f5f3; color:#6b7c77"><i class="bi {{ $type==='categories'?'bi-tags':($type==='products'?'bi-box-seam':'bi-ticket-perforated') }}"></i></span>
                                    <span style="min-width:0">
                                        <span class="d-block fw-semibold" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:280px">{{ $item->name ?? $item->code ?? 'Item #'.$item->id }}</span>
                                        <span class="small text-muted">#{{ $item->id }} @if(isset($item->slug))· {{ Str::limit($item->slug,28) }}@endif</span>
                                    </span>
                                </div>
                            </td>
                            <td class="small">
                                @if(isset($item->category))<span class="badge bg-light text-dark border">{{ $item->category->name }}</span>
                                @elseif($type==='categories')<span class="text-muted">—</span>
                                @else<span class="text-muted">—</span>@endif
                                @if(!empty($item->description))<div class="small text-muted d-none d-lg-block" style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $item->description }}</div>@endif
                            </td>
                            <td class="small">
                                @if(isset($item->price))<span class="fw-semibold">{{ \App\Support\Money::format($item->price) }}</span> @if(!empty($item->discount_price))<span class="text-muted text-decoration-line-through small">{{ \App\Support\Money::format($item->discount_price) }}</span>@endif
                                @elseif(isset($item->value))<span class="fw-semibold">{{ $item->type==='percentage' ? $item->value.'%' : \App\Support\Money::format((int)$item->value*100) }} · {{ $item->type }}</span>
                                @else<span class="text-muted">—</span>@endif
                            </td>
                            <td>
                                @if(array_key_exists('is_active',$item->toArray()))<span class="badge badge-soft" style="background: {{ $item->is_active ? '#dcfce7; color:#166534' : '#fee2e2; color:#991b1b' }}">{{ $item->is_active?'Active':'Inactive' }}</span>
                                @elseif(array_key_exists('is_available',$item->toArray()))<span class="badge badge-soft" style="background: {{ $item->is_available ? '#dcfce7; color:#166534' : '#fee2e2; color:#991b1b' }}">{{ $item->is_available?'Available':'Hidden' }}</span>
                                @else<span class="badge bg-light text-dark border">{{ $item->status ?? '—' }}</span>@endif
                            </td>
                            <td class="pe-4 text-end">
                                @if(in_array($type,['categories','products']))
                                    <form method="POST" action="{{ $type==='categories' ? route('vendor.categories.delete',$item) : route('vendor.products.delete',$item) }}" class="d-inline" onsubmit="return confirm('Delete this {{ $type }}?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No {{ $type }} yet. Use the form above to create your first one.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($items) && method_exists($items,'links'))<div class="card-footer bg-white">{{ $items->links('pagination::bootstrap-5') }}</div>@endif
    </div>
@else
    <div class="card" style="border-radius:16px"><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0" id="dataTable">
        <thead style="background:#f8faf9"><tr class="small text-muted" style="letter-spacing:.04em; text-transform:uppercase"><th class="ps-4 py-3">ID</th><th class="py-3">Name</th><th class="py-3">Info</th><th class="pe-4 py-3 text-end">Action</th></tr></thead>
        <tbody>@forelse($items ?? [] as $item)<tr><td class="ps-4">{{ $item->id }}</td><td class="fw-semibold">{{ $item->name ?? $item->code ?? $item->email ?? 'Item #'.$item->id }}</td><td class="small text-muted" style="max-width:360px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ Str::limit(is_string($item->toArray() ? json_encode($item->toArray()) : ''), 120) }}</td><td class="pe-4 text-end">—</td></tr>@empty<tr><td colspan="4" class="text-center py-5 text-muted">No records.</td></tr>@endforelse</tbody>
    </table></div></div>
    @if(isset($items) && method_exists($items,'links'))<div class="card-footer bg-white">{{ $items->links() }}</div>@endif
    </div>
@endif

<script>
function filterRows(q){
    q = (q||'').toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(tr=>{
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endsection