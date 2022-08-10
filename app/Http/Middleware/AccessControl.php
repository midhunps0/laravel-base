<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permissions, $mode = 'all')
    {
        $permissions = empty($permissions) ? [] : explode("|", $permissions);
        $user = auth()->user();
        // dd($permissions, $user->permissions->pluck('name'));
        $allowed = $mode == 'all';
        $denied = [];
        switch($mode) {
            case 'all':
                foreach ($permissions as $permission) {
                    if (!$user->hasPermissionTo($permission)) {
                        $allowed = false;
                        $denied[] = $permission;
                        break;
                    }
                }
                break;
            case 'any':
                foreach ($permissions as $permission) {
                    if ($user->hasPermissionTo($permission)) {
                        $allowed = true;
                        break;
                    }
                }
                break;
        }

        if (!$allowed) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You don\'t have permission to: '.implode(", ", $denied)
                ]);
            } else {
                return redirect()->back();
            }
        }
        return $next($request);
    }
}
