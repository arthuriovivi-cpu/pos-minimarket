<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'category' => $p->category->name,
                'price' => $p->sell_price,
                'stock' => $p->stock,
                'unit' => $p->unit,
                'image' => $p->image ? asset('storage/'.$p->image) : null,
            ]);

        return view('kasir.index', compact('products'));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => ['required', Rule::in(['tunai', 'qris'])],
            'paid_amount' => 'nullable|integer|min:0',
        ], [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
            'items.*.id.exists' => 'Ada produk yang sudah tidak tersedia.',
            'items.*.qty.min' => 'Kuantitas minimal 1.',
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ]);

        $user = $request->user();

        try {
            $transaction = DB::transaction(function () use ($data, $user) {
                // Gabungkan jika produk yang sama terkirim lebih dari sekali
                $qtyById = [];
                foreach ($data['items'] as $row) {
                    $qtyById[$row['id']] = ($qtyById[$row['id']] ?? 0) + $row['qty'];
                }

                // Kunci baris produk agar dua kasir tidak mengurangi stok yang sama bersamaan
                $products = Product::whereIn('id', array_keys($qtyById))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $total = 0;
                foreach ($qtyById as $id => $qty) {
                    $product = $products[$id];
                    if ($product->stock < $qty) {
                        throw new \RuntimeException(
                            "Stok {$product->name} tidak cukup (tersisa {$product->stock} {$product->unit})."
                        );
                    }
                    // Harga selalu diambil dari database, bukan dari browser
                    $total += $product->sell_price * $qty;
                }

                if ($data['payment_method'] === 'tunai') {
                    $paid = (int) ($data['paid_amount'] ?? 0);
                    if ($paid < $total) {
                        throw new \RuntimeException('Uang yang dibayarkan kurang dari total belanja.');
                    }
                } else {
                    $paid = $total;
                }

                $transaction = Transaction::create([
                    'code' => $this->generateCode(),
                    'user_id' => $user->id,
                    'cashier_name' => $user->name,
                    'payment_method' => $data['payment_method'],
                    'status' => 'success',
                    'total' => $total,
                    'paid_amount' => $paid,
                ]);

                foreach ($qtyById as $id => $qty) {
                    $product = $products[$id];

                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $qty,
                        'cost_price' => $product->cost_price,
                        'sell_price' => $product->sell_price,
                        'subtotal' => $product->sell_price * $qty,
                    ]);

                    $product->decrement('stock', $qty);

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'keluar',
                        'quantity' => $qty,
                        'note' => 'Penjualan '.$transaction->code,
                    ]);
                }

                return $transaction;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'redirect' => route('receipt.show', $transaction),
        ]);
    }

    private function generateCode(): string
    {
        do {
            $code = 'TRX'.now()->format('ymdHis').random_int(100, 999);
        } while (Transaction::where('code', $code)->exists());

        return $code;
    }
}