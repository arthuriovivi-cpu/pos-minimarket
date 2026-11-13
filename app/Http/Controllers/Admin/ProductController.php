<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private const UNITS = ['pcs', 'pack', 'dus', 'botol', 'kg', 'liter'];

    private function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah dipakai produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'unit.required' => 'Satuan wajib dipilih.',
            'unit.in' => 'Satuan tidak valid.',
            'cost_price.required' => 'Harga modal wajib diisi.',
            'cost_price.integer' => 'Harga modal harus berupa angka bulat.',
            'cost_price.min' => 'Harga modal tidak boleh negatif.',
            'sell_price.required' => 'Harga jual wajib diisi.',
            'sell_price.integer' => 'Harga jual harus berupa angka bulat.',
            'sell_price.min' => 'Harga jual tidak boleh negatif.',
            'stock.required' => 'Stok awal wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'min_stock.required' => 'Batas minimum stok wajib diisi.',
            'min_stock.integer' => 'Batas minimum stok harus berupa angka bulat.',
            'min_stock.min' => 'Batas minimum stok tidak boleh negatif.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }

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
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->category_id))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = self::UNITS;

        return view('admin.products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'unit' => ['required', Rule::in(self::UNITS)],
            'cost_price' => 'required|integer|min:0',
            'sell_price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], $this->messages());

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        DB::transaction(function () use ($data) {
            $product = Product::create($data);

            if ($product->stock > 0) {
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $product->stock,
                    'note' => 'Stok awal produk',
                ]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $units = self::UNITS;

        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'unit' => ['required', Rule::in(self::UNITS)],
            'cost_price' => 'required|integer|min:0',
            'sell_price' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], $this->messages());

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}