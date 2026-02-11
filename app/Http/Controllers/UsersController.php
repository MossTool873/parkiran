<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $users = User::with('role')
            ->whereNull('deleted_at')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'              => 'required',
            'nama'                  => 'required',
            'role_id'               => 'required|exists:roles,id',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/users/create')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data tidak valid');
        }

        // Cek user soft deleted
        $user = User::withTrashed()->where('username', $request->username)->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }

            // update data user
            $user->update([
                'name' => $request->nama,
                'role_id' => $request->role_id,
                'password' => Hash::make($request->password),
            ]);
            logAktivitas(
                'Create User (restore): ' . $user->username,
                [
                    'username' => $user->username,
                    'nama'     => $user->name,
                    'role_id'  => $user->role_id,
                    'aksi'     => 'restore'
                ]
            );
        } else {
            // buat user baru
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'name'     => $request->nama,
                'role_id'  => $request->role_id,
            ]);
            logAktivitas(
                'Create User: ' . $user->username,
                [
                    'username' => $user->username,
                    'nama'     => $user->name,
                    'role_id'  => $user->role_id,
                    'aksi'     => 'create'
                ]
            );
        }

        return redirect('/admin/users')
            ->with('success', 'User berhasil ditambahkan');
    }


    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username'              => 'required|unique:users,username,' . $user->id,
            'nama'                  => 'required',
            'role_id'               => 'required|exists:roles,id',
            'password'              => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/users/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'username' => $request->username,
            'name'     => $request->nama,
            'role_id'  => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $oldData = [
            'username' => $user->username,
            'nama'     => $user->name,
            'role_id'  => $user->role_id,
        ];

        $user->update($data);

        logAktivitas(
            'Update User: ' . $user->username,
            [
                'old' => $oldData,
                'new' => $data
            ]
        );

        return redirect('/admin/users')->with('success', 'User berhasil diperbarui');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        logAktivitas(
            'Delete User: ' . $user->username,
            [
                'old' => [
                    'username' => $user->username,
                    'nama'     => $user->name,
                    'role_id'  => $user->role_id,
                ],
                'aksi' => 'delete'
            ]
        );

        $user->delete();

        return redirect('/admin/users')->with('success', 'User berhasil dihapus');
    }
}
