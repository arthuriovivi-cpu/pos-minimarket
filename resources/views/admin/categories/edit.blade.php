@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('heading', 'Edit Kategori')

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}"
          class="bg-white border border-slate-200 rounded-xl p-5 max-w-xl">
        @csrf
        @method('PUT')
        <label class="text-sm text-slate-600">Nama kategori</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required
               class="w-full mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('name')
            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
        @enderror

        <div class="flex gap-3 mt-5">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Simpan Perubahan</button>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-100">Batal</a>
        </div>
    </form>
@endsection