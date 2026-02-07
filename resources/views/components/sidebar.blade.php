<aside 
    class="bg-gray-900 text-white min-h-screen flex flex-col w-64 fixed md:static z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300"
    x-data="{ openMenu: null, sidebarOpen: false }"
    x-cloak
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
>

    {{-- Header + Hamburger --}}
    <div class="flex items-center justify-between p-4 border-b border-gray-700">
<span class="font-semibold">
    <span class="text-2xl">Parkiran</span>
    <span class="text-sm lowercase">app</span>
</span>

        {{-- Hamburger button for mobile --}}
        <button class="md:hidden focus:outline-none" @click="sidebarOpen = !sidebarOpen">
            <i class="bi bi-list text-2xl"></i>
        </button>
    </div>

    {{-- Menu Scrollable --}}
    <nav class="flex-1 mt-4 overflow-y-auto">
        @foreach ($menus as $index => $menu)

            {{-- Menu tanpa children --}}
            @if (!isset($menu['children']))
                <a href="{{ url($menu['route']) }}"
                   class="flex items-center gap-3 px-4 py-2 hover:bg-gray-800 transition rounded
                   {{ request()->is(ltrim($menu['route'], '/').'*') ? 'bg-gray-800' : '' }}">
                    @if(isset($menu['icon']))
                        <i class="{{ $menu['icon'] }}"></i>
                    @endif
                    <span>{{ $menu['label'] }}</span>
                </a>

            {{-- Menu dengan children --}}
            @else
                @php
                    $isActive = collect($menu['children'])
                        ->pluck('route')
                        ->contains(fn($r) => request()->is(ltrim($r,'/').'*'));
                @endphp

                <div class="mt-1">
                    <button
                        @click="openMenu === {{ $index }} ? openMenu = null : openMenu = {{ $index }}"
                        x-init="{{ $isActive ? "openMenu = $index" : '' }}"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded transition"
                    >
                        <div class="flex items-center gap-3">
                            @if(isset($menu['icon']))
                                <i class="{{ $menu['icon'] }}"></i>
                            @endif
                            <span>{{ $menu['label'] }}</span>
                        </div>

                        {{-- Collapsible icon --}}
                        <i :class="openMenu === {{ $index }} ? 'bi bi-chevron-down' : 'bi bi-chevron-right'"></i>
                    </button>

                    <div x-show="openMenu === {{ $index }}"
                         x-transition
                         x-cloak
                         class="ml-6 mt-1 space-y-1">
                        @foreach ($menu['children'] as $child)
                            <a href="{{ url($child['route']) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-800 rounded
                               {{ request()->is(ltrim($child['route'], '/').'*') ? 'bg-gray-800' : '' }}">
                                @if(isset($child['icon']))
                                    <i class="{{ $child['icon'] }}"></i>
                                @endif
                                <span>{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @endforeach
    </nav>

    {{-- Overlay untuk mobile --}}
    <div 
        class="fixed inset-0 bg-black/50 z-40 md:hidden"
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        x-cloak
    ></div>
</aside>
