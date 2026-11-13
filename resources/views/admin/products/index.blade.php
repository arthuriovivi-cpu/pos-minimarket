@extends('layouts.admin')

@section('title', 'Produk')
@section('heading', 'Produk')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau SKU"
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="category_id"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg text-sm font-medium">Cari</button>
            @if (request('q') || request('category_id'))
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm hover:bg-slate-100">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Produk
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Foto</th>
                    <th class="px-4 py-3 font-medium">SKU</th>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium text-right">Modal</th>
                    <th class="px-4 py-3 font-medium text-right">Jual</th>
                    <th class="px-4 py-3 font-medium text-right">Stok</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                                     class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-xs text-slate-400">N/A</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $product->sku }}</td>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category->name }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="{{ $product->stock <= $product->min_stock ? 'text-red-600 font-semibold' : '' }}">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                                  onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                            Tidak ada produk ditemukan. Klik "Tambah Produk" untuk menambah.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection