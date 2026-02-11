@extends('components.app')

@section('title', 'Log Aktivitas')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="mb-4">
    <h1 class="text-2xl font-bold">Log Aktivitas</h1>
    <p class="text-sm text-gray-600">
        Riwayat aktivitas pengguna dalam sistem
    </p>
</div>

{{-- ================= SEARCH ================= --}}
<div class="bg-white border rounded-lg px-6 py-4 mb-6">
    <form method="GET" action="{{ url('/admin/log-aktivitas') }}" class="flex flex-col sm:flex-row gap-3 items-end">
        <input type="text"
               name="keyword"
               value="{{ request('keyword') }}"
               placeholder="Cari aksi, detail, user, IP..."
               class="flex-1 border rounded px-3 py-2">

        <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded">
            Cari
        </button>

        @if(request()->has('keyword'))
            <a href="{{ url('/admin/log-aktivitas') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- ================= TABEL LOG ================= --}}
<div class="bg-white border rounded-lg shadow overflow-x-auto">
    <table class="w-full border-collapse table-auto">
        <thead class="bg-gray-100">
            <tr class="text-left text-sm font-semibold text-gray-700">
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">User</th>
                <th class="px-4 py-2 border">Aksi</th>
                <th class="px-4 py-2 border">IP Address</th>
                <th class="px-4 py-2 border">User Agent</th>
                <th class="px-4 py-2 border">Detail</th>
                <th class="px-4 py-2 border">Waktu</th>
            </tr>
        </thead>

        <tbody class="text-sm">
            @forelse ($logAktivitas as $index => $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border text-center">
                        {{ $logAktivitas->firstItem() + $index }}
                    </td>

                    <td class="px-4 py-2 border font-medium">
                        {{ $log->user->name ?? 'System' }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $log->aksi }}
                    </td>

                    <td class="px-4 py-2 border text-xs break-all">
                        {{ $log->ip_address ?? '-' }}
                    </td>

                    <td class="px-4 py-2 border text-xs break-all max-w-sm">
                        {{ $log->user_agent ?? '-' }}
                    </td>

                    {{-- ===== DETAIL DENGAN SELENGKAPNYA ===== --}}
                    <td class="px-4 py-2 border max-w-md break-words">
                        @php
                            $limit = 30;
                            $detail = $log->detail ?? '-';
                        @endphp

                        @if (strlen($detail) > $limit)
                            <span id="short-{{ $log->id }}">
                                {{ \Illuminate\Support\Str::limit($detail, $limit) }}
                            </span>

                            <span id="full-{{ $log->id }}" class="hidden">
                                {{ $detail }}
                            </span>

                            <button
                                onclick="toggleDetail({{ $log->id }})"
                                id="btn-{{ $log->id }}"
                                class="text-blue-600 text-xs ml-1 hover:underline">
                                Selengkapnya
                            </button>
                        @else
                            {{ $detail }}
                        @endif
                    </td>

                    <td class="px-4 py-2 border text-sm text-gray-600">
                        {{ $log->created_at->format('d-m-Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7"
                        class="px-4 py-6 border text-center text-gray-500">
                        Tidak ada data log
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================= PAGINATION ================= --}}
<div class="mt-4">
    {{ $logAktivitas->withQueryString()->links() }}
</div>

{{-- ================= SCRIPT TOGGLE ================= --}}
<script>
    function toggleDetail(id) {
        const shortText = document.getElementById('short-' + id);
        const fullText  = document.getElementById('full-' + id);
        const button    = document.getElementById('btn-' + id);

        if (fullText.classList.contains('hidden')) {
            shortText.classList.add('hidden');
            fullText.classList.remove('hidden');
            button.innerText = 'Tutup';
        } else {
            fullText.classList.add('hidden');
            shortText.classList.remove('hidden');
            button.innerText = 'Selengkapnya';
        }
    }
</script>

@endsection
