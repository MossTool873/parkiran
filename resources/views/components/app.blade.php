<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My App')</title>

    <!-- ANTI FLASH -->
    <style>
        [x-cloak] { display: none !important; }
        body.loading { visibility: hidden; }
    </style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- QR -->
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

    <script>
        window.addEventListener("load", () => {
            document.body.classList.remove("loading");
        });
    </script>
</head>

<body class="loading bg-gray-100">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <x-sidebar />

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col">

{{-- HEADER --}}
<header class="bg-blue-600 shadow px-4 py-4 flex justify-end items-center">
    <div class="flex items-center space-x-3">
        {{-- NAMA USER --}}
        <span class="text-white font-semibold">
            {{ Auth::user()->name ?? 'User' }}
        </span>

        {{-- ICON PROFIL --}}
        <div class="relative">
            <button id="profileBtn" class="focus:outline-none text-white text-2xl">
                <i class="bi bi-person-circle"></i>
            </button>

            {{-- MENU PROFILE --}}
            <div id="profileMenu"
                 class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                <a href="{{ url('/ganti-password') }}"
                   class="block px-4 py-2 text-sm hover:bg-gray-100">
                    Ganti Password
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>



        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn  = document.getElementById('profileBtn');
    const menu = document.getElementById('profileMenu');
    if (!btn || !menu) return;

    btn.addEventListener('click', e => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
    });

    document.addEventListener('click', e => {
        if (!menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
});
</script>
<style>
@media print {
    body * {
        visibility: hidden;
    }

    .print-area, .print-area * {
        visibility: visible;
    }



    .no-print {
        display: none !important;
    }
}
</style>

</body>
</html>
