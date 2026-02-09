<aside class="bg-gray-900 text-white h-full flex flex-col w-64">

    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-700">
        <span class="font-semibold">
            <span class="text-2xl">Parkiran</span>
            <span class="text-sm lowercase">app</span>
        </span>
    </div>

    <!-- Menu -->
    <nav class="flex-1 mt-4 overflow-y-auto">
        @foreach ($menus as $index => $menu)

            @if (!isset($menu['children']))
                <a href="{{ url($menu['route']) }}"
                   class="flex items-center gap-3 px-4 py-2 hover:bg-gray-800 transition rounded
                   {{ request()->is(ltrim($menu['route'], '/').'*') ? 'bg-gray-800' : '' }}">
                    @if(isset($menu['icon']))
                        <i class="{{ $menu['icon'] }}"></i>
                    @endif
                    <span>{{ $menu['label'] }}</span>
                </a>

            @else
                @php
                    $isActive = collect($menu['children'])
                        ->pluck('route')
                        ->contains(fn($r) => request()->is(ltrim($r,'/').'*'));
                @endphp

                <div x-data="{ openMenu: {{ $isActive ? $index : 'null' }} }" class="mt-1">

                    <button
                        @click="openMenu === {{ $index }} ? openMenu = null : openMenu = {{ $index }}"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded transition"
                    >
                        <div class="flex items-center gap-3">
                            @if(isset($menu['icon']))
                                <i class="{{ $menu['icon'] }}"></i>
                            @endif
                            <span>{{ $menu['label'] }}</span>
                        </div>

                        <i :class="openMenu === {{ $index }} ? 'bi bi-chevron-down' : 'bi bi-chevron-right'"></i>
                    </button>

                    <div x-show="openMenu === {{ $index }}"
                         x-transition
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

</aside>
