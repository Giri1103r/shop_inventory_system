<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\LeftMenu;
use App\Models\UserPermission;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $module, $permission): Response
    {

        if (Auth::check()) {

            $menuDetails = LeftMenu::where('namekey', $module)->first();

            if ($menuDetails != null) {

                $menuId = $menuDetails->id;

                $whereArray = array(
                    'menu_id' =>  $menuId,
                    'role_id' =>  Auth::user()->role,
                );

                $permissionList =  UserPermission::where($whereArray)->first();

                if ($permissionList != null &&  $permissionList != '') {

                    $permissionApprove =  $permissionList->$permission;

                    if ($permissionApprove == 1) {
                        return $next($request);
                    }
                }
            }
        }


        return abort(403, 'Unauthorized. You do not have permission to '.$permission.' in ' . $module);
    }
}
