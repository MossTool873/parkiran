<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
</head>

<body class="bg-gray-100">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Konten utama --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>

</body>
</html>

<style>
@media print {
    body * {
        visibility: hidden;
    }

    .print-area, .print-area * {
        visibility: visible;
    }

    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
    }

    .no-print {
        display: none !important;
    }
}
</style>
