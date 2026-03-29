<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $this->requirePermission('roles.view');
        return view('admin.role.index');
    }

    public function getList()
    {
        $this->requirePermission('roles.view');
        $roles = Role::query();

        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('action', function ($role) {
                $id = $role->id;
                
                $editBtn = '<a href="/admin/role/edit/' . $id . '" 
                    class="btn btn-sm mx-2 px-3 py-2 btn-primary" 
                    title="edit">
                    <i class="fas fa-edit"></i>
                </a>';

                $deleteBtn = '<a href="#" 
                    class="btn btn-danger btn-sm px-3 py-2 delete-btn" 
                    data-id="' . $id . '" 
                    title="delete">
                    <i class="fa fa-trash-alt"></i>
                </a>';

                return '<div class="action-btn" role="group">' . $editBtn . ' ' . $deleteBtn . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $this->requirePermission('roles.create');
        return view('admin.role.create');
    }

    public function store(Request $request)
    {
        $this->requirePermission('roles.create');
        $request->validate([
            'name' => 'required|unique:roles,name',
            'code' => 'nullable|unique:roles,code',
        ]);

        Role::create([
            'name'       => $request->name,
            'code'       => $request->code ?? str($request->name)->slug()->value(),
            'guard_name' => 'web',
        ]);

        return response()->json([
            'message' => 'Role created successfully',
        ]);
    }

    public function edit(string $id)
    {
        $this->requirePermission('roles.update');
        $role = Role::findOrFail($id);
        $allPermissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        $groupedPermissions = $this->groupPermissions($allPermissions, $rolePermissions);

        return view('admin.role.edit', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, string $id)
    {
        $this->requirePermission('roles.update');

        $role = Role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        // Always sync — passing [] clears all permissions when none are selected
        $role->syncPermissions($request->input('permissions', []));

        return response()->json([
            'message' => 'Role updated successfully',
        ]);
    }

    public function destroy(string $id)
    {
        $this->requirePermission('roles.delete');
        $role = Role::findOrFail($id);
        
        if ($role->users()->count() > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Cannot delete role assigned to users',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    private function groupPermissions($permissions, $selectedNames = [])
    {
        $actionMap = [
            'view'   => 'view',
            'create' => 'create',
            'update' => 'edit',
            'edit'   => 'edit',
            'delete' => 'delete',
        ];

        $groups = [];

        foreach ($permissions as $perm) {
            $name = $perm->name;
            
            // Handle menu permissions separately or group them
            if (str_starts_with($name, 'menu.')) {
                $module = 'System Navigation';
                $action = 'other';
            } else {
                $dotPos = strpos($name, '.');
                if ($dotPos === false) {
                    $module = $name;
                    $action = 'other';
                } else {
                    $module = substr($name, 0, $dotPos);
                    $actionPart = strtolower(substr($name, $dotPos + 1));
                    $action = $actionMap[$actionPart] ?? 'other';
                }
            }

            $moduleKey = strtolower($module);
            if (!isset($groups[$moduleKey])) {
                $groups[$moduleKey] = [
                    'module'      => $moduleKey,
                    'displayName' => str_replace(['_', '-'], ' ', ucfirst($moduleKey)),
                    'view'        => null,
                    'create'      => null,
                    'edit'        => null,
                    'delete'      => null,
                    'other'       => [],
                ];
            }

            $permData = [
                'id'       => $perm->id,
                'name'     => $perm->name,
                'label'    => $perm->label ?? $perm->name,
                'selected' => in_array($perm->name, $selectedNames),
            ];

            if ($action !== 'other' && $groups[$moduleKey][$action] === null) {
                $groups[$moduleKey][$action] = $permData;
            } else {
                $groups[$moduleKey]['other'][] = $permData;
            }
        }

        // Sort: System Navigation first, then alphabetical
        uksort($groups, function ($a, $b) {
            if ($a === 'system navigation') return -1;
            if ($b === 'system navigation') return 1;
            return strcmp($a, $b);
        });

        return $groups;
    }
}
