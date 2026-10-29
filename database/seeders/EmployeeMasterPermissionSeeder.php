<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeMasterPermissionSeeder extends Seeder
{
    /**
     * Opens up the Employee Management > Master screens (Employee Status,
     * Employee Type, Document Type) for the Superadmin role. The menu rows
     * already exist in template_left_menu, but the role has to carry the menu
     * ids before the sidebar renders them, and the CRUD flags before the
     * add / edit / view buttons appear.
     */
    public function run(): void
    {
        $roleId = 1;

        /* Employee Management (11) > Master (12) > the three masters. */
        $menuIds = DB::table('template_left_menu')
            ->whereIn('link', [
                'employee-management/master/employee-status/list',
                'employee-management/master/employee-type/list',
                'employee-management/master/document-type/list',
            ])
            ->pluck('id')
            ->all();

        if (empty($menuIds)) {
            return;
        }

        $parentIds = DB::table('template_left_menu')
            ->whereIn('id', $menuIds)
            ->pluck('parent_id')
            ->all();

        $required = array_unique(array_merge($menuIds, $parentIds, [11]));

        $role = DB::table('template_user_role')->where('id', $roleId)->first();

        if ($role) {
            $current = $role->role_permission == '' || $role->role_permission === null
                ? []
                : explode(',', $role->role_permission);

            $merged = array_unique(array_merge($current, $required));

            DB::table('template_user_role')
                ->where('id', $roleId)
                ->update([
                    'role_permission' => implode(',', $merged),
                    'updated_at' => now(),
                ]);
        }

        foreach ($menuIds as $menuId) {
            $permissions = [
                'add' => 1,
                'edit' => 1,
                'view' => 1,
                'delete' => 1,
                'export' => 1,
            ];

            $exists = DB::table('master_role_permission')
                ->where('role_id', $roleId)
                ->where('menu_id', $menuId)
                ->exists();

            if ($exists) {
                DB::table('master_role_permission')
                    ->where('role_id', $roleId)
                    ->where('menu_id', $menuId)
                    ->update($permissions + ['updated_at' => now()]);

                continue;
            }

            DB::table('master_role_permission')->insert($permissions + [
                'role_id' => $roleId,
                'menu_id' => $menuId,
                'created_by' => 1,
                'updated_by' => 1,
                'status' => 1,
                'trash' => 'NO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
