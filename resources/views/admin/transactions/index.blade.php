@extends('layouts.admin')

@section('title', 'Riwayat Transaksi')
@section('heading', 'Riwayat Transaksi')

@section('content')
    <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex flex-wrap items-end gap-2 mb-4">
        <div>
            <label class="text-xs text-slate-500">Kode transaksi</label>
            <input type="text" name="code" value="{{ request('code') }}" placeholder="TRX..."
                   class="block border border-slate-300 rounded-lg px-3 py-2 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="text-xs text-slate-500">Nama kasir</label>
            <input type="text" name="cashier" value="{{ request('cashier') }}" placeholder="Nama kasir"
                   class="block border border-slate-300 rounded-lg px-3 py-2 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
        @if (request()->hasAny(['code', 'cashier', 'date_from', 'date_to']))
            <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm hover:bg-slate-100">Reset</a>
        @endif
    </form>

    @if ($errors->any())
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
            @foreach ($errors->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="text-sm text-slate-500">Transaksi berhasil (sesuai filter)</div>
            <div class="text-2xl font-semibold text-blue-700 mt-1">{{ $summaryCount }}</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="text-sm text-slate-500">Total penjualan (sesuai filter)</div>
            <div class="text-2xl font-semibold text-blue-700 mt-1">Rp {{ number_format($summaryTotal, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Kode</th>
                    <th class="px-4 py-3 font-medium">Tanggal & Jam</th>
                    <th class="px-4 py-3 font-medium">Kasir</th>
                    <th class="px-4 py-3 font-medium">Produk</th>
                    <th class="px-4 py-3 font-medium text-right">Jml Produk</th>
                    <th class="px-4 py-3 font-medium text-right">Total</th>
                    <th class="px-4 py-3 font-medium">Metode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($transactions as $trx)
                    <tr class="align-top">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('receipt.show', $trx) }}" class="text-blue-600 hover:underline">{{ $trx->code }}</a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $trx->cashier_name }}</td>
                        <td class="px-4 py-3">
                            @foreach ($trx->items as $item)
                                <div>{{ $item->product_name }} <span class="text-slate-500">x{{ $item->quantity }}</span></div>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-right">{{ $trx->items->sum('quantity') }}</td>
                        <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $trx->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada transaksi yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
@endsection