<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\LeftMenu;
use App\Models\UserPermission;
use App\Models\Master\UserRole;

class UserPermissionController extends Controller
{

    private $userrole;
    private $userpermission;
    private $leftmenu;

    public function __construct()
    {

        $this->userrole = new UserRole();
        $this->userpermission = new UserPermission();
        $this->leftmenu = new LeftMenu();
    }


    public function index(Request $request)
    {
        $menuList =  $this->leftmenu->list();

        $roleList = $this->userrole->get();

        $menu_array = array();
        $i = 0;
        foreach ($menuList as $key => $value) {
            $menu_array[$value->parent_id][$i]['id'] = $value->id;
            $menu_array[$value->parent_id][$i]['name'] = $value->name;
            $menu_array[$value->parent_id][$i]['link'] = $value->link;
            $menu_array[$value->parent_id][$i]['icon'] = $value->icon;
            $menu_array[$value->parent_id][$i]['is_parent'] = $value->is_parent;
            $menu_array[$value->parent_id][$i]['parent_id'] = $value->parent_id;
            $menu_array[$value->parent_id][$i]['permission'] = $value->permission;
            $menu_array[$value->parent_id][$i]['sort_order'] = $value->sort_order;
            $i++;
        }

        $data = array(
            'menuList' =>  getRoleMenu($menuList),
            'roleList' =>  $roleList,
        );

        return view('master.userpermission.list', $data);
    }


    public function getUserPermission(Request $request)
    {
        try {
            $menu_permission_list = [];
            $roleId = decryptId($request->id);
            $roleDetails = $this->userrole->find($roleId);

            if ($roleDetails->role_permission == null || $roleDetails->role_permission == '') {
                $menuParent = $childPermission = $userpermission = [];
            } else {
                $userpermission = string_to_array($roleDetails->role_permission);
                $menuParent = $this->leftmenu->whereIn('id', $userpermission)->where('is_parent', 1)->get();
                $childPermission = $this->userpermission->where('role_id', $roleId)->get();
            }
            if (count($menuParent) > 0) {
                foreach ($menuParent as $menu) {
                    $menu_permission_list[] = 'menu_' . $menu->id . '_all';
                }
            }

            if (count($childPermission) > 0) {
                foreach ($childPermission as $childmenu) {

                    $rolePermissions = $childmenu->role_permissions;
                    $rolePermissions = json_decode($rolePermissions, true);
                    if (json_last_error() === JSON_ERROR_NONE) {

                        if (isset($rolePermissions['add']) && $rolePermissions['add'] == 1) {
                            $menu_permission_list[] = 'menu_' . $childmenu->menu_id . '_add';
                        }
                        if (isset($rolePermissions['edit']) && $rolePermissions['edit'] == 1) {
                            $menu_permission_list[] = 'menu_' . $childmenu->menu_id . '_edit';
                        }
                        if (isset($rolePermissions['delete']) && $rolePermissions['delete'] == 1) {
                            $menu_permission_list[] = 'menu_' . $childmenu->menu_id . '_delete';
                        }
                        if (isset($rolePermissions['view']) && $rolePermissions['view'] == 1) {
                            $menu_permission_list[] = 'menu_' . $childmenu->menu_id . '_view';
                        }
                        if (isset($rolePermissions['export']) && $rolePermissions['export'] == 1) {
                            $menu_permission_list[] = 'menu_' . $childmenu->menu_id . '_export';
                        }
                    } else {
                        // Log JSON decoding error
                        Log::error('Failed to decode role permissions JSON for child menu ID ' . $childmenu->menu_id . ': ' . json_last_error_msg());
                        // Handle JSON decoding error
                        throw new Exception('Failed to decode role permissions JSON: ' . json_last_error_msg());
                    }
                }
            }

            return response()->json(['status' => 'success', 'userpermission' => $menu_permission_list], 200);
        } catch (Exception $ex) {
            Log::error('Error fetching user permissions: ' . $ex->getMessage());
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time. Error: ' . $ex->getMessage()], 406);
        }
    }


    public function updateUserPermission(Request $request)
    {

        try {
            $menuItems = [];
            $permission = [];
            $permission[] = 1;

            $role_id = decryptId($request->role);

            $this->userpermission->where('role_id', $role_id)->update([
                'role_permissions' => json_encode([
                    'add' => 0,
                    'edit' => 0,
                    'delete' => 0,
                    'view' => 0,
                    'export' => 0
                ])
            ]);

            foreach ($request->input() as $key => $value) {
                if (str_starts_with($key, 'menu_')) {
                    $menudetails = string_to_array($key, '_');
                    if (!str_ends_with($key, '_all')) {
                        $menuItems[$menudetails[1]]['role_id'] = $role_id;
                        $menuItems[$menudetails[1]]['menu_id'] = $menudetails[1];
                        $menuItems[$menudetails[1]]['role_permissions'][$menudetails[2]] = 1;

                    }
                    $permission[] = $menudetails[1];
                }
            }
            $permission_unique = array_unique($permission);
            $this->userrole->where('id', $role_id)->update(['role_permission' => array_to_string($permission_unique)]);

            foreach ($menuItems as $menu) {
                $menu['role_permissions'] = json_encode($menu['role_permissions']);
                $menu['created_by'] = Auth::id();
                $menu['updated_by'] = Auth::id();
                $menu['status'] = 1;
                $menu['trash'] = 'NO';

                $this->userpermission->updateOrInsert(
                    ['role_id' => $menu['role_id'], 'menu_id' => $menu['menu_id']],
                    $menu
                );
            }

            Session::flash('success', 'User Role Permision successfully updated!');

            return redirect(admin_url('administration/permission/list'));
        } catch (\Throwable $th) {

            Session::flash('error', 'Something went wrong, Please try after sometimes');

            return redirect(admin_url('administration/permission/list'));
        }
    }
}
