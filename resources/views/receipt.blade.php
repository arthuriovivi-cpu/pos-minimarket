<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="bg-slate-100 py-8">
    <div class="max-w-sm mx-auto bg-white border border-slate-200 rounded-xl p-6 font-mono text-sm text-slate-800">
        <div class="text-center mb-4">
            <div class="text-lg font-bold">Minimarket POS</div>
            <div class="text-xs text-slate-500">Struk Pembelian</div>
        </div>

        <div class="text-xs space-y-1 border-y border-dashed border-slate-300 py-3 mb-3">
            <div class="flex justify-between"><span>No. Transaksi</span><span>{{ $transaction->code }}</span></div>
            <div class="flex justify-between"><span>Tanggal</span><span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span>Kasir</span><span>{{ $transaction->cashier_name }}</span></div>
        </div>

        <table class="w-full text-xs mb-3">
            <thead>
                <tr class="text-left border-b border-dashed border-slate-300">
                    <th class="pb-1 font-semibold">Produk</th>
                    <th class="pb-1 font-semibold text-right">Qty</th>
                    <th class="pb-1 font-semibold text-right">Harga</th>
                    <th class="pb-1 font-semibold text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr class="align-top">
                        <td class="py-1 pr-2">{{ $item->product_name }}</td>
                        <td class="py-1 text-right">{{ $item->quantity }}</td>
                        <td class="py-1 text-right">{{ number_format($item->sell_price, 0, ',', '.') }}</td>
                        <td class="py-1 text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-dashed border-slate-300 pt-3 space-y-1">
            <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between font-bold text-base"><span>Total</span><span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Metode Bayar</span><span>{{ $transaction->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}</span></div>
            @if ($transaction->payment_method === 'tunai')
                <div class="flex justify-between"><span>Dibayar</span><span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Kembalian</span><span>Rp {{ number_format($transaction->paid_amount - $transaction->total, 0, ',', '.') }}</span></div>
            @endif
        </div>

        <div class="text-center text-xs text-slate-500 mt-5">Terima kasih telah berbelanja</div>
    </div>

    <div class="no-print max-w-sm mx-auto mt-4 flex gap-3">
        <button type="button" onclick="window.print()"
                class="flex-1 border border-slate-300 bg-white rounded-lg py-2 text-sm hover:bg-slate-50">Cetak Struk</button>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 text-sm font-medium">Kembali ke Dashboard</a>
        @else
            <a href="{{ route('kasir.index') }}"
               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 text-sm font-medium">Transaksi Baru</a>
        @endif
    </div>
</body>
</html>