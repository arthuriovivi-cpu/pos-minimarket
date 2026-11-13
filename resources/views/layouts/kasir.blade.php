<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') - Minimarket POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 text-slate-700">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
        <div>
            <span class="text-lg font-semibold text-blue-700">Minimarket POS</span>
            <span class="text-xs text-slate-500 ml-2">Kasir</span>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-sm text-right">
                <div class="font-medium">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm border border-slate-300 rounded-lg px-4 py-2 hover:bg-slate-100">Keluar</button>
            </form>
        </div>
    </header>

    @yield('content')
</body>
</html>