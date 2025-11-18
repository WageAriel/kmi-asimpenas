<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', Rule::in(['mitra', 'admin', 'super admin'])],
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'user' => $user
        ], 201);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent super admin from being modified by others
        if ($user->role === 'super admin' && auth()->user()->role !== 'super admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengubah Super Admin.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['mitra', 'admin', 'super admin'])],
            'password' => 'nullable|min:8',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Update nama perusahaan di data_mitra jika user adalah mitra
        if ($user->role === 'mitra' && $user->mitra) {
            $user->mitra->update([
                'nama_perusahaan' => $validated['name']
            ]);
        }

        return response()->json([
            'message' => 'User berhasil diperbarui',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent super admin from being deleted
        if ($user->role === 'super admin') {
            return response()->json([
                'message' => 'Super Admin tidak dapat dihapus.'
            ], 403);
        }

        // Prevent user from deleting themselves
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}

