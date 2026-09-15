<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function edit()
    {
        return view('user.profil.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $u = Auth::user();
        $v = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$u->id_user.',id_user',
            'no_hp' => 'nullable|max:20',
            'alamat' => 'nullable',
        ]);
        $u->update($v);
        return back()->with('success', 'Profil diperbarui');
    }

    public function changePassword(Request $request)
    {
        $v = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($v['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah']);
        }

        Auth::user()->update(['password' => Hash::make($v['password'])]);
        return back()->with('success', 'Password diganti');
    }
}