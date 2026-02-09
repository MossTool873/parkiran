<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>

    <!-- Anti Flash -->
    <style>
        [x-cloak] { display: none !important; }
        body.loading { visibility: hidden; }
    </style>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        window.addEventListener("load", () => {
            document.body.classList.remove("loading");
        });
    </script>
</head>

<body class="loading bg-gray-100">

<div x-data="{ sidebarOpen: false }" class="min-h-screen flex relative">

    <!-- ================= OVERLAY (Mobile) ================= -->
    <div 
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-40 z-40 lg:hidden"
        x-cloak>
    </div>

    <!-- ================= SIDEBAR ================= -->
    <aside
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50
               transform transition-transform duration-300 ease-in-out
               lg:static lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <x-sidebar />

    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="flex-1 flex flex-col w-0">

        <!-- ================= HEADER ================= -->
        <header class="bg-blue-600 shadow px-4 py-4 flex justify-between items-center">

            <!-- Hamburger Button (Mobile Only) -->
            <button 
                @click="sidebarOpen = true"
                class="lg:hidden text-white text-2xl">
                <i class="bi bi-list"></i>
            </button>

            <!-- Spacer (Desktop) -->
            <div class="hidden lg:block"></div>

            <!-- Profile Section -->
            <div class="flex items-center space-x-3 relative">

                <span class="text-white font-semibold hidden sm:block">
                    {{ Auth::user()->name ?? 'User' }}
                </span>

                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="text-white text-2xl flex items-center gap-1 focus:outline-none">
                        <i class="bi bi-person-circle"></i>
                        <i class="bi bi-caret-down-fill text-sm"></i>
                    </button>

                    <div 
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50"
                        x-cloak>

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

        <!-- ================= PAGE CONTENT ================= -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>

    </div>

</div>

<!-- ================= PRINT STYLE ================= -->
<style>
@media print {
    body * { visibility: hidden; }

    .print-area,
    .print-area * {
        visibility: visible;
    }

    .no-print {
        display: none !important;
    }
}
</style>

</body>
</html>
