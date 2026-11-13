@extends('layouts.admin')

@section('title', 'Kategori')
@section('heading', 'Kategori Produk')

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}"
          class="bg-white border border-slate-200 rounded-xl p-5 mb-6 max-w-xl">
        @csrf
        <label class="text-sm text-slate-600">Tambah kategori baru</label>
        <div class="flex gap-3 mt-1">
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Minuman" required
                   class="flex-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg font-medium">Simpan</button>
        </div>
        @error('name')
            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
        @enderror
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden max-w-3xl">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama Kategori</th>
                    <th class="px-4 py-3 font-medium">Jumlah Produk</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3">{{ $category->name }}</td>
                        <td class="px-4 py-3">{{ $category->products_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                  onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">Belum ada kategori. Tambahkan lewat form di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 max-w-3xl">{{ $categories->links() }}</div>
@endsection