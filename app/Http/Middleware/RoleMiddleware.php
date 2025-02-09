<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // ✅ التحقق من أن المستخدم مسجل دخول
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // ✅ التأكد من أن المستخدم لديه الدور المطلوب
        if (Auth::user()->role !== $role) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
