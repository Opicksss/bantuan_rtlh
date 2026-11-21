<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ManagementAkunController extends Controller
{
    public function admin()
    {
        $admin = User::where('role', 'admin')->get();
        return view('management-akun.index', compact('admin'));
    }

    public function verifikator()
    {
        $verifikators = User::where('role', 'verifikator')->get();
        return view('management-akun.verifikator', compact('verifikators'));
    }

    public function store()
    {
        try {
            request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,verifikator',
            ]);

            User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => bcrypt(request('password')),
                'role' => request('role'),
            ]);

            return redirect()->back()->with('success', 'Akun berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                // 'password' => 'nullable|string|min:8|confirmed',
                // 'role' => 'required|in:admin,verifikator',
            ]);

            // $user->name = $request->name;
            // $user->email = $request->email;
            // if ($request->filled('password')) {
            //     $user->password = bcrypt($request->password);
            // }
            $user->role = $request->role;
            $user->save();

            return redirect()->back()->with('success', 'Akun berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }
}
