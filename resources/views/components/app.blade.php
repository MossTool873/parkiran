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

    {{-- Main --}}
    <div class="flex-1 flex flex-col">

        {{-- HEADER --}}
        <header class="bg-white shadow px-6 py-3 flex justify-end items-center">
            <div class="relative">
                {{-- Profile Image --}}
                <button id="profileBtn" class="focus:outline-none">
                    <img src="{{ asset('images/profile.png') }}" alt="Profile" class="w-10 h-10 rounded-full border object-cover">
                </button>

                {{-- Dropdown --}}
                <div
                    id="profileMenu"
                    class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg overflow-hidden z-50"
                >

                    <a href="{{ url('/ganti-password') }}"
                       class="block px-4 py-2 text-sm hover:bg-gray-100">
                        Ganti Password
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>
</div>

{{-- SCRIPT HEADER --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn  = document.getElementById('profileBtn');
        const menu = document.getElementById('profileMenu');

        if (!btn || !menu) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    });
</script>

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
