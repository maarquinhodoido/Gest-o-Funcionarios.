<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $companyId = $user->company_id ?? $user->getCompanyId();
        $request->route()->setParameter('company_id', $companyId);

        return $next($request);
    }
}
