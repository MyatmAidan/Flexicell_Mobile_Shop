<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $this->requirePermission('users.view');
        $user = User::all();

        return view('admin.user.index', compact('user'));
    }

    public function create()
    {
        $this->requirePermission('users.create');
        return view('admin.user.create');
    }

    public function store(UserCreateRequest $request)
    {
        $this->requirePermission('users.create');
        $role = Role::where('code', $request->role ?? 'staff')->first();

        $user = User::create([
            'role_id'           => $role?->id,
            'name'              => $request->name,
            'email'             => $request->email,
            'username'          => $request->username ?? explode('@', $request->email)[0],
            'password'          => Hash::make($request->password),
            'phone'             => $request->phone,
            'address'           => $request->address,
            'status'            => 1,
            'is_primary_account' => false,
        ]);

        if ($role) {
            $user->assignRole($role);
        }

        return response()->json([
            'message' => 'User created successfully',
        ]);
    }

    public function getList()
    {
        $this->requirePermission('users.view');
        $user = User::query()->with('assignedRole');

        return DataTables::of($user)
            ->addIndexColumn()
            ->addColumn('plus-icon', function ($user) {
                return '';
            })
            ->editColumn('name', function ($user) {
                $url = route('admin.user.show', $user->id);
                return '<a href="' . $url . '" class="fw-semibold text-decoration-none">' . htmlspecialchars($user->name, ENT_QUOTES) . '</a>';
            })
            ->addColumn('role', function ($user) {
                return $user->assignedRole?->name ?? 'N/A';
            })
            ->addColumn('action', function ($user) {
                $id = $user->id;
                $showUrl = route('admin.user.show', $id);

                $viewBtn = '<a href="' . $showUrl . '" 
                    class="btn btn-sm px-3 py-2 btn-outline-info" 
                    title="View Profile">
                    <i class="fas fa-eye"></i>
                </a>';

                $editBtn = '<a href="#" 
                    class="btn btn-sm px-3 py-2 btn-primary edit-user-btn" 
                    title="Edit"
                    data-id="' . $id . '"
                    data-name="' . htmlspecialchars($user->name, ENT_QUOTES) . '"
                    data-email="' . htmlspecialchars($user->email, ENT_QUOTES) . '"
                    data-phone="' . htmlspecialchars((string) $user->phone, ENT_QUOTES) . '"
                    data-address="' . htmlspecialchars((string) $user->address, ENT_QUOTES) . '"
                    data-role="' . htmlspecialchars($user->assignedRole?->code ?? '', ENT_QUOTES) . '"
                >
                    <i class="fas fa-edit"></i>
                </a>';

                $deleteBtn = '<a href="#" 
                    class="btn btn-danger btn-sm px-3 py-2 delete-btn" 
                    data-id="' . $id . '" 
                    title="Delete">
                    <i class="fa fa-trash-alt"></i>
                </a>';

                return '<div class="d-flex gap-1" role="group">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['name', 'action'])
            ->make(true);
    }

    public function show(string $id)
    {
        $this->requirePermission('users.view');
        $user = User::with('assignedRole')->findOrFail($id);

        return view('admin.user.show', compact('user'));
    }

    public function edit(string $id)
    {
        $this->requirePermission('users.update');
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, string $id)
    {
        $this->requirePermission('users.update');
        try {
            $user = User::findOrFail($id);
            $role = Role::where('code', $request->role)->first();

            $data = [
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'address' => $request->address,
                'role_id' => $role?->id,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            
            if ($role) {
                $user->syncRoles([$role]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'User updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $this->requirePermission('users.delete');
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
