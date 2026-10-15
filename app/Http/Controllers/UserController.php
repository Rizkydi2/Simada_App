<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Memastikan hanya Admin Sistem yang dapat mengakses fitur manajemen pengguna (UC-01 Extension 1b & UAT-01).
     */
    protected function authorizeAdmin(): void
    {
        if (auth()->check() && auth()->user()->role !== 'Admin Sistem') {
            abort(403, 'Akses Ditolak (403 Forbidden): Hanya Admin Sistem yang berwenang mengakses Manajemen Pengguna (UAT-01).');
        }
    }

    /**
     * Menampilkan daftar pengguna dan hak akses (UC-01 / WF-08).
     * Mendukung filter peran (role), pencarian (search nama/NIP/email/OPD),
     * serta ringkasan statistik jumlah pengguna.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = User::query();

        // 1. Filter berdasarkan Peran (Role)
        $selectedRole = $request->query('role');
        if ($request->filled('role') && in_array($selectedRole, User::ROLES)) {
            $query->where('role', $selectedRole);
        }

        // 2. Pencarian berdasarkan Nama, NIP, Email, atau OPD
        $search = trim($request->query('search', $request->query('q', '')));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('opd', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->get();

        // 3. Ringkasan Statistik Pengguna
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'Aktif')->count();
        $inactiveUsers = User::where('status', 'Nonaktif')->count();

        $roleCounts = [];
        foreach (User::ROLES as $role) {
            $roleCounts[$role] = User::where('role', $role)->count();
        }

        $roles = User::ROLES;
        $statuses = User::STATUSES;

        return view('users', compact(
            'users',
            'roles',
            'statuses',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'roleCounts',
            'selectedRole',
            'search'
        ));
    }

    /**
     * Menyimpan data pengguna baru ke database (UC-01 Main Course Step 5-6).
     * Melakukan validasi kelengkapan, keunikan NIP/email, hashing password, dan penugasan peran.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:30|unique:users,nip',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:' . implode(',', User::ROLES),
            'opd' => 'nullable|string|max:100',
            'status' => 'required|string|in:' . implode(',', User::STATUSES),
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar pada pengguna lain.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar pada pengguna lain.',
            'password.required' => 'Kata sandi awal wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
            'role.required' => 'Peran (Role) pengguna wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid sesuai spesifikasi RBAC SIMADA.',
            'status.required' => 'Status pengguna wajib dipilih.',
            'status.in' => 'Status pengguna tidak valid.',
        ]);

        User::create([
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'opd' => $validated['opd'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('users')
            ->with('success', 'Pengguna baru "' . $validated['name'] . '" berhasil didaftarkan dengan peran ' . $validated['role'] . '.');
    }

    /**
     * Memperbarui data profil, peran (role), OPD, status, dan opsional password (UC-01 Extension 3a).
     * Meliputi proteksi integritas agar peran/status Admin Sistem terakhir tidak dapat dialihkan atau dinonaktifkan.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:' . implode(',', User::ROLES),
            'opd' => 'nullable|string|max:100',
            'status' => 'required|string|in:' . implode(',', User::STATUSES),
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar pada pengguna lain.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar pada pengguna lain.',
            'password.min' => 'Kata sandi baru minimal terdiri dari 6 karakter.',
            'role.required' => 'Peran (Role) pengguna wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid sesuai spesifikasi RBAC SIMADA.',
            'status.required' => 'Status pengguna wajib dipilih.',
            'status.in' => 'Status pengguna tidak valid.',
        ]);

        // Proteksi: Mencegah perubahan role atau penonaktifan Admin Sistem terakhir
        if ($user->role === 'Admin Sistem' && ($validated['role'] !== 'Admin Sistem' || $validated['status'] !== 'Aktif')) {
            $otherActiveAdmins = User::where('role', 'Admin Sistem')
                ->where('status', 'Aktif')
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveAdmins === 0) {
                return redirect()->route('users')
                    ->with('error', 'Tidak dapat mengubah peran atau menonaktifkan akun Admin Sistem terakhir demi keberlangsungan sistem.');
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'opd' => $validated['opd'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users')
            ->with('success', 'Data dan hak akses pengguna "' . $user->name . '" berhasil diperbarui.');
    }

    /**
     * Menghapus data pengguna dari sistem (UC-01).
     * Dilengkapi proteksi keamanan agar tidak dapat menghapus akun Admin Sistem terakhir.
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $user = User::findOrFail($id);

        // Proteksi: Larangan menghapus akun Admin Sistem terakhir
        if ($user->role === 'Admin Sistem') {
            $adminCount = User::where('role', 'Admin Sistem')->count();
            if ($adminCount <= 1) {
                return redirect()->route('users')
                    ->with('error', 'Tidak dapat menghapus akun Admin Sistem terakhir demi keamanan dan integritas sistem.');
            }
        }

        $deletedName = $user->name;
        $user->delete();

        return redirect()->route('users')
            ->with('success', 'Pengguna "' . $deletedName . '" berhasil dihapus dari database.');
    }
}
