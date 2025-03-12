<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeftMenu;
use App\Models\UserPermission;
use App\Models\Master\UserRole;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $module, $permission)
    {
        if (Auth::check()) {
            $menuDetails = LeftMenu::where('namekey', $module)->first();
            if ($menuDetails != null) {
                $menuId = $menuDetails->id;
                $roleIds = string_to_array(Auth::user()->role);
                $userRoles =  UserRole::whereIn('id', $roleIds)->get();
                foreach ($userRoles as $role) {
                    $whereArray = [
                        'menu_id' => $menuId,
                        'role_id' => $role,
                    ];
                }

                $permissionList = UserPermission::where($whereArray)->first();

                if ($permissionList != null) {
                    if (is_string($permissionList->role_permissions)) {
                        $rolePermissions = json_decode($permissionList->role_permissions, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            return response()->json(['error' => 'Invalid permissions format'], 500);
                        }
                    } elseif (is_array($permissionList->role_permissions)) {
                        $rolePermissions = $permissionList->role_permissions;
                    } else {
                        return response()->json(['error' => 'Invalid permissions format'], 500);
                    }

                    if (isset($rolePermissions[$permission]) && $rolePermissions[$permission] == 1) {
                        return $next($request);
                    }
                }
            }
        }

        return abort(403, 'Unauthorized. You do not have permission to ' . $permission . ' in ' . $module);
    }
}
