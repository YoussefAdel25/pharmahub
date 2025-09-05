<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $data = $request->except('password');

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $userIds = $request->user_ids;

        if ($userIds && is_array($userIds)) {
            User::whereIn('id', $userIds)->delete();
        }

        return response()->json(['success' => true]);
    }
}
