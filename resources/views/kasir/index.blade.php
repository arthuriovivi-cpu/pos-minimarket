@extends('layouts.kasir')

@section('title', 'Kasir')

@section('content')
<div x-data="pos()" x-cloak class="flex" style="height: calc(100vh - 4rem);">

    {{-- Kiri: daftar produk --}}
    <section class="flex-1 flex flex-col p-6 overflow-hidden">
        <input type="text" x-model="search" placeholder="Cari produk berdasarkan nama atau SKU"
               class="w-full border border-slate-300 rounded-lg px-4 py-3 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

        <div class="flex-1 overflow-y-auto">
            <template x-if="products.length === 0">
                <div class="text-center text-slate-500 py-16">Belum ada produk. Minta admin menambahkan produk terlebih dahulu.</div>
            </template>
            <template x-if="products.length > 0 && filtered.length === 0">
                <div class="text-center text-slate-500 py-16">Produk tidak ditemukan.</div>
            </template>

            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="p in filtered" :key="p.id">
                    <button type="button" @click="add(p)" :disabled="p.stock <= 0"
                            class="bg-white border border-slate-200 rounded-xl p-3 text-left hover:border-blue-400 disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="p.image">
                            <img :src="p.image" :alt="p.name" class="w-full h-28 object-cover rounded-lg mb-2">
                        </template>
                        <template x-if="!p.image">
                            <div class="w-full h-28 rounded-lg bg-slate-100 flex items-center justify-center text-xs text-slate-400 mb-2">Tanpa foto</div>
                        </template>
                        <div class="text-sm font-medium text-slate-800 leading-tight" x-text="p.name"></div>
                        <div class="text-xs text-slate-500" x-text="p.sku"></div>
                        <div class="text-blue-700 font-semibold mt-1" x-text="rupiah(p.price)"></div>
                        <div class="text-xs mt-1" :class="p.stock <= 0 ? 'text-red-600' : 'text-slate-500'"
                             x-text="p.stock <= 0 ? 'Stok habis' : 'Stok: ' + p.stock + ' ' + p.unit"></div>
                    </button>
                </template>
            </div>
        </div>
    </section>

    {{-- Kanan: keranjang --}}
    <aside class="w-96 bg-white border-l border-slate-200 flex flex-col">
        <div class="px-5 py-4 border-b border-slate-200 font-semibold text-slate-800">Keranjang</div>

        <div class="flex-1 overflow-y-auto px-5 py-3">
            <template x-if="cart.length === 0">
                <div class="text-center text-slate-500 text-sm py-10">Keranjang kosong. Klik produk untuk menambahkan.</div>
            </template>

            <template x-for="item in cart" :key="item.id">
                <div class="py-3 border-b border-slate-100">
                    <div class="flex justify-between gap-2">
                        <div class="text-sm font-medium text-slate-800" x-text="item.name"></div>
                        <button type="button" @click="remove(item)" class="text-xs text-red-600 hover:underline">Hapus</button>
                    </div>
                    <div class="text-xs text-slate-500" x-text="rupiah(item.price) + ' / ' + item.unit"></div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-1">
                            <button type="button" @click="setQty(item, item.qty - 1)" class="w-8 h-8 border border-slate-300 rounded-lg hover:bg-slate-100">-</button>
                            <input type="number" min="1" :max="item.stock" :value="item.qty"
                                   @change="setQty(item, $event.target.value)"
                                   class="w-14 h-8 text-center border border-slate-300 rounded-lg">
                            <button type="button" @click="setQty(item, item.qty + 1)" class="w-8 h-8 border border-slate-300 rounded-lg hover:bg-slate-100">+</button>
                        </div>
                        <div class="text-sm font-semibold" x-text="rupiah(item.price * item.qty)"></div>
                    </div>
                </div>
            </template>
        </div>

        <div class="border-t border-slate-200 p-5 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Subtotal</span>
                <span x-text="rupiah(total)"></span>
            </div>
            <div class="flex justify-between text-lg font-semibold text-slate-800">
                <span>Total</span>
                <span class="text-blue-700" x-text="rupiah(total)"></span>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="payment = 'tunai'"
                        :class="payment === 'tunai' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-300'"
                        class="border rounded-lg py-2 text-sm font-medium">Tunai</button>
                <button type="button" @click="payment = 'qris'"
                        :class="payment === 'qris' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-300'"
                        class="border rounded-lg py-2 text-sm font-medium">QRIS</button>
            </div>

            <div x-show="payment === 'tunai'">
                <label class="text-sm text-slate-600">Uang diterima (Rp)</label>
                <input type="number" min="0" x-model.number="paid"
                       class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex justify-between text-sm mt-2">
                    <span class="text-slate-500">Kembalian</span>
                    <span :class="change < 0 ? 'text-red-600' : 'text-slate-800'" x-text="change < 0 ? 'Uang kurang' : rupiah(change)"></span>
                </div>
            </div>

            <div x-show="payment === 'qris'" class="text-sm text-slate-600 bg-blue-50 border border-blue-100 rounded-lg p-3">
                Minta pelanggan memindai QRIS toko dan membayar sebesar total. Setelah pembayaran masuk, klik tombol di bawah.
            </div>

            <div x-show="error" class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3" x-text="error"></div>

            <button type="button" @click="checkout()" :disabled="processing || cart.length === 0"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white py-3 rounded-lg font-medium"
                    x-text="processing ? 'Memproses...' : (payment === 'qris' ? 'Konfirmasi Pembayaran QRIS' : 'Bayar Tunai')"></button>
        </div>
    </aside>
</div>

<script>
function pos() {
    return {
        products: @json($products),
        cart: [],
        search: '',
        payment: 'tunai',
        paid: '',
        processing: false,
        error: '',

        get filtered() {
            const q = this.search.trim().toLowerCase();
            if (!q) return this.products;
            return this.products.filter(p =>
                p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q)
            );
        },
        get total() {
            return this.cart.reduce((sum, i) => sum + i.price * i.qty, 0);
        },
        get change() {
            return (Number(this.paid) || 0) - this.total;
        },
        rupiah(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        },
        add(p) {
            this.error = '';
            const found = this.cart.find(i => i.id === p.id);
            if (found) {
                if (found.qty < found.stock) found.qty++;
                else this.error = 'Stok ' + p.name + ' hanya tersisa ' + p.stock + '.';
            } else {
                this.cart.push({ id: p.id, name: p.name, price: p.price, unit: p.unit, stock: p.stock, qty: 1 });
            }
        },
        setQty(item, value) {
            this.error = '';
            let q = parseInt(value);
            if (isNaN(q) || q < 1) q = 1;
            if (q > item.stock) {
                q = item.stock;
                this.error = 'Stok ' + item.name + ' hanya tersisa ' + item.stock + '.';
            }
            item.qty = q;
        },
        remove(item) {
            this.cart = this.cart.filter(i => i.id !== item.id);
        },
        async checkout() {
            this.error = '';
            if (this.cart.length === 0) return;
            if (this.payment === 'tunai' && this.change < 0) {
                this.error = 'Uang yang diterima kurang dari total.';
                return;
            }
            this.processing = true;
            try {
                const res = await fetch('{{ route('kasir.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        items: this.cart.map(i => ({ id: i.id, qty: i.qty })),
                        payment_method: this.payment,
                        paid_amount: this.payment === 'tunai' ? Number(this.paid) || 0 : null,
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.error = data.message || 'Transaksi gagal diproses.';
                    this.processing = false;
                    return;
                }
                window.location.href = data.redirect;
            } catch (e) {
                this.error = 'Tidak bisa terhubung ke server. Coba lagi.';
                this.processing = false;
            }
        },
    };
}
</script>
@endsection