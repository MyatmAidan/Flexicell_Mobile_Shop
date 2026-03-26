<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $user = User::all();
        return view('admin.user.index', compact('user'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(UserCreateRequest $request)
    {
        User::create([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role'  => $request->role ?? 'staff',
        ]);

        return response()->json([
            'message' => 'User created successfully'
        ]);
    }

    public function getList()
    {
        $user = User::all();
        return DataTables::of($user)
            ->addColumn('plus-icon', function ($user) {
                return null;
            })
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                $id = $user->id;

                $permBtn = '<a href="/admin/user/' . $id . '/permissions" 
                    class="btn btn-sm mx-1 px-3 py-2 btn-info" 
                    title="Manage Permissions">
                    <i class="fas fa-shield-alt"></i>
                </a>';

                $editBtn = '<a href="#" 
                    class="btn btn-sm mx-2 px-3 py-2 btn-primary edit-user-btn" 
                    title="edit"
                    data-id="' . $id . '"
                    data-name="' . htmlspecialchars($user->name, ENT_QUOTES) . '"
                    data-email="' . htmlspecialchars($user->email, ENT_QUOTES) . '"
                    data-phone="' . htmlspecialchars($user->phone, ENT_QUOTES) . '"
                    data-address="' . htmlspecialchars($user->address, ENT_QUOTES) . '"
                    data-role="' . htmlspecialchars($user->role, ENT_QUOTES) . '"
                >
                    <i class="fas fa-edit"></i>
                </a>';

                $deleteBtn = '<a href="#" 
                    class="btn btn-danger btn-sm px-3 py-2 delete-btn" 
                    data-id="' . $id . '" 
                    title="delete">
                    <i class="fa fa-trash-alt"></i>
                </a>';

                return '<div class="action-btn" role="group">' . $permBtn . ' ' . $editBtn . ' ' . $deleteBtn . '</div>';
            })

            ->rawColumns(['logo', 'action', 'plus-icon'])
            ->make(true);
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => $request->role,
            ];

            $user->update($data);

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }



    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
