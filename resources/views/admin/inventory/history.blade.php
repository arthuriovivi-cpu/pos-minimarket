@extends('layouts.admin')

@section('title', 'Riwayat Stok')
@section('heading', 'Riwayat Stok')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
        <form method="GET" action="{{ route('admin.inventory.history') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <label class="text-xs text-slate-500">Produk</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama atau SKU"
                       class="block border border-slate-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="text-xs text-slate-500">Jenis</label>
                <select name="type"
                        class="block border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="masuk" @selected(request('type') === 'masuk')>Stok masuk</option>
                    <option value="keluar" @selected(request('type') === 'keluar')>Stok keluar</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500">Dari tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="block border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="text-xs text-slate-500">Sampai tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="block border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Terapkan</button>
            @if (request()->hasAny(['q', 'type', 'date_from', 'date_to']))
                <a href="{{ route('admin.inventory.history') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm hover:bg-slate-100">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.inventory.index') }}"
           class="px-4 py-2 border border-slate-300 bg-white rounded-lg text-sm font-medium hover:bg-slate-100">
            Kembali ke Inventaris
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
            @foreach ($errors->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Tanggal & Jam</th>
                    <th class="px-4 py-3 font-medium">Produk</th>
                    <th class="px-4 py-3 font-medium">Jenis</th>
                    <th class="px-4 py-3 font-medium text-right">Jumlah</th>
                    <th class="px-4 py-3 font-medium">Catatan</th>
                    <th class="px-4 py-3 font-medium">Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($movements as $movement)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $movement->product->name }}</div>
                            <div class="text-xs text-slate-500">{{ $movement->product->sku }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($movement->type === 'masuk')
                                <span class="inline-block border rounded-full px-2 py-0.5 text-xs font-medium bg-green-50 text-green-700 border-green-200">Masuk</span>
                            @else
                                <span class="inline-block border rounded-full px-2 py-0.5 text-xs font-medium bg-red-50 text-red-700 border-red-200">Keluar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium {{ $movement->type === 'masuk' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $movement->type === 'masuk' ? '+' : '-' }}{{ $movement->quantity }} {{ $movement->product->unit }}
                        </td>
                        <td class="px-4 py-3">{{ $movement->note ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $movement->user->name ?? 'Akun sudah dihapus' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat stok yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $movements->links() }}</div>
@endsection