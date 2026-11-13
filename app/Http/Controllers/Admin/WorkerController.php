<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = User::where('role', 'kasir')
            ->withCount('transactions')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.workers.index', compact('workers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|max:100',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Password otomatis di-hash oleh cast 'hashed' di model User
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'kasir',
        ]);

        return redirect()->route('admin.workers.index')
            ->with('success', 'Akun kasir berhasil dibuat.');
    }

    public function destroy(User $worker)
    {
        if ($worker->role !== 'kasir') {
            return back()->with('error', 'Hanya akun kasir yang bisa dihapus.');
        }

        // Riwayat transaksi tetap aman: user_id menjadi NULL,
        // nama kasir sudah tersimpan di kolom cashier_name.
        $worker->delete();

        return back()->with('success', 'Akun kasir berhasil dihapus. Riwayat transaksinya tetap tersimpan.');
    }
}