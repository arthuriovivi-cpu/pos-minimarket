@php $editing = isset($product); @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="text-sm text-slate-600">Nama produk</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
               class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm text-slate-600">SKU / kode produk</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required
               class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('sku') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm text-slate-600">Kategori</label>
        <select name="category_id" required
                class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm text-slate-600">Satuan</label>
        <select name="unit" required
                class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach ($units as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $product->unit ?? 'pcs') === $unit)>{{ ucfirst($unit) }}</option>
            @endforeach
        </select>
        @error('unit') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm text-slate-600">Harga modal (Rp)</label>
        <input type="number" name="cost_price" min="0" value="{{ old('cost_price', $product->cost_price ?? '') }}" required
               class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('cost_price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm text-slate-600">Harga jual (Rp)</label>
        <input type="number" name="sell_price" min="0" value="{{ old('sell_price', $product->sell_price ?? '') }}" required
               class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('sell_price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm text-slate-600">{{ $editing ? 'Stok saat ini' : 'Stok awal' }}</label>
        @if ($editing)
            <input type="text" value="{{ $product->stock }} {{ $product->unit }}" disabled
                   class="w-full mt-1 border border-slate-200 bg-slate-100 rounded-lg px-3 py-2 text-slate-500">
            <p class="text-xs text-slate-500 mt-1">Stok diubah lewat menu Inventaris agar tercatat.</p>
        @else
            <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}" required
                   class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('stock') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label class="text-sm text-slate-600">Batas minimum stok</label>
        <input type="number" name="min_stock" min="0" value="{{ old('min_stock', $product->min_stock ?? 5) }}" required
               class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('min_stock') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="text-sm text-slate-600">Deskripsi (opsional)</label>
        <textarea name="description" rows="3"
                  class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="text-sm text-slate-600">Foto produk (jpg, png, webp, maks 2 MB)</label>
        @if ($editing && $product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                 class="w-24 h-24 object-cover rounded-lg border border-slate-200 mt-2">
            <p class="text-xs text-slate-500 mt-1">Pilih file baru untuk mengganti foto.</p>
        @endif
        <input type="file" name="image" accept="image/*"
               class="block w-full mt-2 text-sm border border-slate-300 rounded-lg p-2">
        @error('image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>