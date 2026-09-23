@props(['type'=>'info','dismissible'=>true])
@php $map=['success'=>'success','danger'=>'danger','warning'=>'warning','info'=>'info','primary'=>'primary']; $t=$map[$type]??'info'; @endphp
<div class="alert alert-{{ $t }} {{ $dismissible?'alert-dismissible fade show':'' }}" role="alert">
    {{ $slot }}
    @if($dismissible)<button type="button" class="btn-close" data-bs-dismiss="alert"></button>@endif
</div>
