<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
</head>
<body class="flex bg-gray-100">

    {{-- Sidebar --}}
    <x-sidebar />

    {{-- Konten utama --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</body>
</html>