@extends('layouts.admin')

@section('title', 'Kelola Stok')
@section('heading', 'Kelola Stok: ' . $product->name)

@section('content')
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-6 max-w-2xl">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-slate-500">SKU</div>
                <div class="font-medium">{{ $product->sku }}</div>
            </div>
            <div>
                <div class="text-slate-500">Kategori</div>
                <div class="font-medium">{{ $product->category->name }}</div>
            </div>
            <div>
                <div class="text-slate-500">Stok saat ini</div>
                <div class="text-2xl font-semibold text-blue-700">{{ $product->stock }} {{ $product->unit }}</div>
            </div>
            <div>
                <div class="text-slate-500">Batas minimum</div>
                <div class="text-2xl font-semibold text-slate-800">{{ $product->min_stock }}</div>
            </div>
        </div>
    </div>

    {{-- Tambah stok --}}
    <form method="POST" action="{{ route('admin.inventory.add', $product) }}"
          class="bg-white border border-slate-200 rounded-xl p-5 mb-6 max-w-2xl">
        @csrf
        <div class="text-sm font-medium text-slate-700 mb-3">Tambah stok (stok masuk)</div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-slate-600">Jumlah ({{ $product->unit }})</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity') }}" required
                       class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('quantity') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Catatan (opsional)</label>
                <input type="text" name="note" value="{{ old('note') }}" placeholder="Contoh: Kiriman supplier"
                       class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('note') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Tambah Stok</button>
    </form>

    {{-- Batas minimum --}}
    <form method="POST" action="{{ route('admin.inventory.min', $product) }}"
          class="bg-white border border-slate-200 rounded-xl p-5 max-w-2xl">
        @csrf
        @method('PUT')
        <div class="text-sm font-medium text-slate-700 mb-1">Batas minimum stok</div>
        <p class="text-xs text-slate-500 mb-3">Produk dianggap menipis kalau stoknya sama dengan atau di bawah angka ini.</p>

        <div class="flex gap-3 items-start">
            <div>
                <input type="number" name="min_stock" min="0" value="{{ old('min_stock', $product->min_stock) }}" required
                       class="w-40 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('min_stock') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="border border-slate-300 px-4 py-2 rounded-lg font-medium hover:bg-slate-100">Simpan Batas</button>
        </div>
    </form>

    <div class="mt-6">
        <a href="{{ route('admin.inventory.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Inventaris</a>
    </div>
@endsection