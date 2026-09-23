<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function __construct(private readonly TenantContext $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->is_active && $user->hasRole('vendor', 'staff'), 403);
        abort_if($user->restaurant_id === null || $user->restaurant === null, 403, 'A restaurant membership is required.');

        $this->tenant->set($user->restaurant);

        return $next($request);
    }
}
