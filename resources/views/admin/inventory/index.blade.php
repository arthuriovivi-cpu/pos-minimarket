@extends('layouts.admin')

@section('title', 'Inventaris')
@section('heading', 'Inventaris')

@section('content')
    {{-- Kartu ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 max-w-4xl">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="text-sm text-slate-500">Total Produk</div>
            <div class="text-3xl font-semibold text-blue-700 mt-1">{{ $totalProducts }}</div>
        </div>
        <a href="{{ route('admin.inventory.index', ['status' => 'restock']) }}"
           class="bg-white border border-slate-200 rounded-xl p-5 hover:border-blue-400">
            <div class="text-sm text-slate-500">Perlu Restok (di bawah batas minimum)</div>
            <div class="text-3xl font-semibold text-amber-600 mt-1">{{ $needRestock }}</div>
        </a>
        <a href="{{ route('admin.inventory.index', ['status' => 'habis']) }}"
           class="bg-white border border-slate-200 rounded-xl p-5 hover:border-blue-400">
            <div class="text-sm text-slate-500">Stok Habis</div>
            <div class="text-3xl font-semibold text-red-600 mt-1">{{ $outOfStock }}</div>
        </a>
    </div>

    {{-- Filter dan tautan riwayat --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau SKU"
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="status"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="restock" @selected(request('status') === 'restock')>Perlu restok</option>
                <option value="habis" @selected(request('status') === 'habis')>Stok habis</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg text-sm font-medium">Cari</button>
            @if (request('q') || request('status'))
                <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm hover:bg-slate-100">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.inventory.history') }}"
           class="px-4 py-2 border border-slate-300 bg-white rounded-lg text-sm font-medium hover:bg-slate-100">
            Lihat Riwayat Stok
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">SKU</th>
                    <th class="px-4 py-3 font-medium">Produk</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium text-right">Stok</th>
                    <th class="px-4 py-3 font-medium text-right">Batas Min.</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    @php
                        $badge = [
                            'habis' => ['Habis', 'bg-red-50 text-red-700 border-red-200'],
                            'hampir_habis' => ['Hampir habis', 'bg-orange-50 text-orange-700 border-orange-200'],
                            'menipis' => ['Menipis', 'bg-amber-50 text-amber-700 border-amber-200'],
                            'aman' => ['Aman', 'bg-green-50 text-green-700 border-green-200'],
                        ][$product->stock_status];
                    @endphp
                    <tr>
                        <td class="px-4 py-3">{{ $product->sku }}</td>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category->name }}</td>
                        <td class="px-4 py-3 text-right">{{ $product->stock }} {{ $product->unit }}</td>
                        <td class="px-4 py-3 text-right">{{ $product->min_stock }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block border rounded-full px-2 py-0.5 text-xs font-medium {{ $badge[1] }}">{{ $badge[0] }}</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.inventory.stock', $product) }}" class="text-blue-600 hover:underline">Kelola Stok</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">Tidak ada produk yang cocok dengan pencarian atau filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection