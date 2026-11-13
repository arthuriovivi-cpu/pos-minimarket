@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="text-sm text-slate-500">Total Produk</div>
            <div class="text-3xl font-semibold text-blue-700 mt-1">{{ $totalProducts }}</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="text-sm text-slate-500">Total Kategori</div>
            <div class="text-3xl font-semibold text-blue-700 mt-1">{{ $totalCategories }}</div>
        </div>
    </div>
@endsection