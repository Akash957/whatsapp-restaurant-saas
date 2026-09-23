@props(['title'=>null,'subtitle'=>null])
<div class="card shadow-sm border-0 h-100">
    @if($title)
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 fw-semibold">{{ $title }}</h6>
            @if($subtitle)<small class="text-muted">{{ $subtitle }}</small>@endif
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
