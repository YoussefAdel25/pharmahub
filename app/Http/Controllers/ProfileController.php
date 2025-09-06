<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Region\Region;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $regions = Region::all();
        return view('profile.show', compact('user', 'regions'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|confirmed|min:8',
        ];
        if ($user->role === 'customer') {
            $rules['region_id'] = 'required|exists:regions,id';
        }
        $request->validate($rules);


        $user->update($request->only('name', 'email', 'region_id'));

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return back()->with('success', 'Profile updated successfully!');
    }
}
