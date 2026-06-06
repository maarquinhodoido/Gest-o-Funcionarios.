<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompanyContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = auth('api')->user()) {
            $companyId = $user->company_id ?? $user->getCompanyId() ?? null;
            if ($companyId) {
                $request->merge(['company_id' => $companyId]);
            }
        }

        return $next($request);
    }
}
