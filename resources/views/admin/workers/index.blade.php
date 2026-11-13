@extends('layouts.admin')

@section('title', 'Pekerja')
@section('heading', 'Akun Pekerja (Kasir)')

@section('content')
    <form method="POST" action="{{ route('admin.workers.store') }}"
          class="bg-white border border-slate-200 rounded-xl p-5 mb-6 max-w-3xl">
        @csrf
        <div class="text-sm font-medium text-slate-700 mb-3">Buat akun kasir baru</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="text-sm text-slate-600">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Password (min. 8 karakter)</label>
                <input type="password" name="password" required
                       class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Buat Akun</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden max-w-3xl">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium text-right">Jumlah Transaksi</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($workers as $worker)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $worker->name }}</td>
                        <td class="px-4 py-3">{{ $worker->email }}</td>
                        <td class="px-4 py-3 text-right">{{ $worker->transactions_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.workers.destroy', $worker) }}" class="inline"
                                  onsubmit="return confirm('Hapus akun {{ $worker->name }} secara permanen? Riwayat transaksinya tetap tersimpan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada akun kasir. Buat lewat form di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 max-w-3xl">{{ $workers->links() }}</div>
@endsection