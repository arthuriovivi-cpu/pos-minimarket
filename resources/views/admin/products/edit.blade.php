@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('heading', 'Edit Produk')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
          class="bg-white border border-slate-200 rounded-xl p-6 max-w-4xl">
        @csrf
        @method('PUT')
        @include('admin.products._form')

        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Simpan Perubahan</button>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-100">Batal</a>
        </div>
    </form>
@endsection