<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($request->status === 'restock', fn ($query) => $query->whereColumn('stock', '<=', 'min_stock'))
            ->when($request->status === 'habis', fn ($query) => $query->where('stock', '<=', 0))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalProducts = Product::count();
        $needRestock = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $outOfStock = Product::where('stock', '<=', 0)->count();

        return view('admin.inventory.index', compact('products', 'totalProducts', 'needRestock', 'outOfStock'));
    }

    public function history(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:masuk,keluar',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_from.date' => 'Tanggal awal tidak valid.',
            'date_to.date' => 'Tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $movements = InventoryMovement::with(['product', 'user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->whereHas('product', function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.inventory.history', compact('movements'));
    }

    public function stockForm(Product $product)
    {
        $product->load('category');

        return view('admin.inventory.stock', compact('product'));
    }

    public function addStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:100000',
            'note' => 'nullable|string|max:150',
        ], [
            'quantity.required' => 'Jumlah stok wajib diisi.',
            'quantity.integer' => 'Jumlah stok harus berupa angka bulat.',
            'quantity.min' => 'Jumlah stok minimal 1.',
            'quantity.max' => 'Jumlah stok terlalu besar.',
            'note.max' => 'Catatan maksimal 150 karakter.',
        ]);

        DB::transaction(function () use ($product, $data) {
            // Kunci baris produk agar tidak bentrok dengan transaksi kasir yang sedang berjalan
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            $locked->increment('stock', $data['quantity']);

            InventoryMovement::create([
                'product_id' => $locked->id,
                'user_id' => auth()->id(),
                'type' => 'masuk',
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?: 'Penambahan stok manual',
            ]);
        });

        return redirect()->route('admin.inventory.index')
            ->with('success', "Stok {$product->name} berhasil ditambah {$data['quantity']} {$product->unit}.");
    }

    public function updateMinStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'min_stock' => 'required|integer|min:0|max:100000',
        ], [
            'min_stock.required' => 'Batas minimum stok wajib diisi.',
            'min_stock.integer' => 'Batas minimum stok harus berupa angka bulat.',
            'min_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        $product->update($data);

        return redirect()->route('admin.inventory.stock', $product)
            ->with('success', 'Batas minimum stok berhasil diperbarui.');
    }
}