<aside class="w-64 bg-gray-900 text-white min-h-screen flex flex-col">
    {{-- HEADER --}}
    <div class="p-4 text-lg font-semibold border-b border-gray-700">
        My App
    </div>

    {{-- MENU --}}
    <nav class="mt-4 space-y-1 flex-1">
        @foreach ($menus as $menu)

            {{-- MENU TANPA CHILD --}}
            @if (!isset($menu['children']))
                <a
                    href="{{ url($menu['route']) }}"
                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-800 transition
                    {{ request()->is(ltrim($menu['route'], '/').'*') ? 'bg-gray-800' : '' }}"
                >
                    <span>{{ $menu['icon'] ?? '' }}</span>
                    <span>{{ $menu['label'] }}</span>
                </a>

            {{-- MENU DENGAN CHILD --}}
            @else
                <div
                    x-data="{
                        open: {{ collect($menu['children'])
                            ->pluck('route')
                            ->contains(fn($r) => request()->is(ltrim($r,'/').'*'))
                            ? 'true' : 'false' }}
                    }"
                >
                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 transition"
                    >
                        <div class="flex items-center gap-3">
                            <span>{{ $menu['icon'] ?? '' }}</span>
                            <span>{{ $menu['label'] }}</span>
                        </div>
                        <span x-text="open ? '▾' : '▸'"></span>
                    </button>

                    <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
                        @foreach ($menu['children'] as $child)
                            <a
                                href="{{ url($child['route']) }}"
                                class="block px-4 py-2 text-sm hover:bg-gray-800 rounded
                                {{ request()->is(ltrim($child['route'], '/').'*') ? 'bg-gray-800' : '' }}"
                            >
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @endforeach
    </nav>

    {{-- LOGOUT --}}
    <div class="border-t border-gray-700 p-2">
        <form action="{{ url('/logout') }}" method="POST">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center gap-2 px-4 py-2 rounded
                       text-red-400 hover:bg-gray-800 hover:text-red-300 transition"
            >
                <span>🚪</span>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<script src="//unpkg.com/alpinejs" defer></script>