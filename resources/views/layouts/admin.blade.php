<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Minimarket POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-700">
@php
    $menus = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Dashboard',
         'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'label' => 'Kategori',
         'icon' => 'M7 7h.01M3 5a2 2 0 012-2h5l9 9a2 2 0 010 3l-5 5a2 2 0 01-3 0L3 10V5z'],
        ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'label' => 'Produk',
         'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['route' => 'admin.inventory.index', 'match' => 'admin.inventory.*', 'label' => 'Inventaris',
         'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ['route' => 'admin.workers.index', 'match' => 'admin.workers.*', 'label' => 'Pekerja',
         'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0">
        <div class="px-6 py-5 border-b border-slate-200">
            <div class="text-xl font-semibold text-blue-700">Minimarket POS</div>
            <div class="text-xs text-slate-500">Panel Admin</div>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            @foreach ($menus as $menu)
                @php $active = request()->routeIs($menu['match']); @endphp
                <a href="{{ route($menu['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                          {{ $active ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu['icon'] }}"/>
                    </svg>
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-500 mb-3">{{ auth()->user()->email }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-sm border border-slate-300 rounded-lg py-2 hover:bg-slate-100">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Konten --}}
    <main class="flex-1 ml-64 p-8">
        <h1 class="text-2xl font-semibold text-slate-800 mb-6">@yield('heading')</h1>

        @if (session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>