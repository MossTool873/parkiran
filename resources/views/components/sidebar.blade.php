<aside class="w-64 bg-gray-900 text-white min-h-screen flex flex-col"
       x-data="{ openMenu: null }"
       x-cloak>

    <div class="p-4 text-lg font-semibold border-b border-gray-700">
        Parkiran
    </div>

    <nav class="mt-4 space-y-1 flex-1">
        @foreach ($menus as $index => $menu)

            @if (!isset($menu['children']))
                <a href="{{ url($menu['route']) }}"
                   class="flex items-center gap-3 px-4 py-2 hover:bg-gray-800 transition
                   {{ request()->is(ltrim($menu['route'], '/').'*') ? 'bg-gray-800' : '' }}">
                    <span>{{ $menu['icon'] ?? '' }}</span>
                    <span>{{ $menu['label'] }}</span>
                </a>

            @else
                @php
                    $isActive = collect($menu['children'])
                        ->pluck('route')
                        ->contains(fn($r) => request()->is(ltrim($r,'/').'*'));
                @endphp

                <div>
                    <button
                        @click="openMenu === {{ $index }} ? openMenu = null : openMenu = {{ $index }}"
                        x-init="{{ $isActive ? "openMenu = $index" : '' }}"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 transition"
                    >
                        <div class="flex items-center gap-3">
                            <span>{{ $menu['icon'] ?? '' }}</span>
                            <span>{{ $menu['label'] }}</span>
                        </div>
                        <span x-text="openMenu === {{ $index }} ? '▾' : '▸'"></span>
                    </button>

                    <div x-show="openMenu === {{ $index }}"
                         x-transition
                         x-cloak
                         class="ml-6 mt-1 space-y-1">

                        @foreach ($menu['children'] as $child)
                            <a href="{{ url($child['route']) }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-800 rounded
                               {{ request()->is(ltrim($child['route'], '/').'*') ? 'bg-gray-800' : '' }}">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @endforeach
    </nav>
</aside>
