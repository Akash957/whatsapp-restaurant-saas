@extends('layouts.panel')
@section('page_title','Subscriptions')
@section('content')
<div class="card"><div class="card-body p-0"><table class="table mb-0"><thead class="table-light"><tr><th>Restaurant</th><th>Plan</th><th>Status</th><th>Period</th></tr></thead><tbody>
@forelse($subs as $s)<tr><td>{{ $s->restaurant->name ?? '-' }}</td><td>{{ $s->plan->name ?? '-' }}</td><td><span class="badge bg-primary">{{ $s->status }}</span></td><td class="small">{{ $s->starts_at->format('d M Y') }} → {{ $s->ends_at->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">No subscriptions</td></tr>@endforelse
</tbody></table></div></div>
@endsection