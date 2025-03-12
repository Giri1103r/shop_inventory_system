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
            if ($menuDetails) {
                $menuId = $menuDetails->id;
                $roleIds = string_to_array(Auth::user()->role);
                $userRoles = UserRole::whereIn('id', $roleIds)->pluck('id')->toArray();
                $permissionList = UserPermission::where('menu_id', $menuId)
                    ->whereIn('role_id', $userRoles)
                    ->get();
                if ($permissionList->isNotEmpty()) {
                    $rolePermissions = [];
                    foreach ($permissionList as $permissionItem) {
                        $decodedPermissions = [];

                        if (is_string($permissionItem->role_permissions)) {
                            $decodedPermissions = json_decode($permissionItem->role_permissions, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                return response()->json(['error' => 'Invalid permissions format'], 500);
                            }
                        } elseif (is_array($permissionItem->role_permissions)) {
                            $decodedPermissions = $permissionItem->role_permissions;
                        }

                        foreach ($decodedPermissions as $key => $value) {
                            if (!isset($rolePermissions[$key]) || $value == 1) {
                                $rolePermissions[$key] = $value;
                            }
                        }
                    }

                    if (!empty($rolePermissions[$permission]) && $rolePermissions[$permission] == 1) {
                        return $next($request);
                    }
                }
            }
        }

        return abort(403, 'Unauthorized. You do not have permission to ' . $permission . ' in ' . $module);
    }
}
