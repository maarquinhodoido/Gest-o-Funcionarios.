<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Application\Services\AuditService;

class AuditMiddleware
{
    public function __construct(
        private AuditService $auditService,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
