<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends Controller
{
    /**
     * Show the permission editor for a specific user.
     */
    public function edit(string $id)
    {
        $user        = User::findOrFail($id);
        $permissions = $user->getAllPermissionsWithStatus();

        $groupedPermissions = $this->groupPermissions($permissions);

        return view('admin.user.permissions', compact('user', 'groupedPermissions'));
    }

    private function groupPermissions($permissions)
    {
        $actionMap = [
            'view'   => 'view',
            'create' => 'create',
            'update' => 'edit',
            'edit'   => 'edit',
            'delete' => 'delete',
            'manage' => 'edit',
        ];

        $groups = [];

        foreach ($permissions as $perm) {
            $name = $perm['name'];
            $dotPos = strpos($name, '.');

            if ($dotPos === false) {
                $module = $name;
                $action = 'other';
            } else {
                $module = substr($name, 0, $dotPos);
                $actionPart = strtolower(substr($name, $dotPos + 1));
                $action = $actionMap[$actionPart] ?? 'other';
            }

            if (!isset($groups[$module])) {
                $groups[$module] = [
                    'module'      => $module,
                    'displayName' => str_replace(['_', '-'], ' ', ucfirst($module)),
                    'view'        => null,
                    'create'      => null,
                    'edit'        => null,
                    'delete'      => null,
                    'other'       => [],
                ];
            }

            if ($action !== 'other' && $groups[$module][$action] === null) {
                $groups[$module][$action] = $perm;
            } else {
                $groups[$module]['other'][] = $perm;
            }
        }

        ksort($groups);

        return $groups;
    }

    /**
     * Save the permission overrides for a user.
     *
     * Expects: permissions[permission_id] = 'inherit' | 'grant' | 'revoke'
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Delete all existing overrides for this user
        DB::table('user_permissions')->where('user_id', $user->id)->delete();

        $incoming = $request->input('permissions', []);

        $rows = [];
        foreach ($incoming as $permId => $type) {
            if (in_array($type, ['grant', 'revoke'])) {
                $rows[] = [
                    'user_id'       => $user->id,
                    'permission_id' => $permId,
                    'type'          => $type,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
            // 'inherit' = no row needed, just skip
        }

        if (!empty($rows)) {
            DB::table('user_permissions')->insert($rows);
        }

        return response()->json([
            'status'  => true,
            'message' => "Permissions updated for {$user->name}.",
        ]);
    }
}
