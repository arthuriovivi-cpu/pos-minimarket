<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Minimarket POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center">
    <form method="POST" action="{{ route('login') }}" class="bg-white w-96 p-8 rounded-xl shadow border border-slate-200">
        @csrf
        <h1 class="text-2xl font-semibold text-blue-700 mb-1">Minimarket POS</h1>
        <p class="text-sm text-slate-500 mb-6">Masuk untuk melanjutkan</p>

        @error('email')
            <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded p-2">{{ $message }}</div>
        @enderror

        <label class="text-sm text-slate-600">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full mb-4 mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

        <label class="text-sm text-slate-600">Password</label>
        <input type="password" name="password" required
               class="w-full mb-6 mt-1 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">Masuk</button>
    </form>
</body>
</html>