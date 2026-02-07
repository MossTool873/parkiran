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

{{-- ================= FILTER TANGGAL ================= --}}
<div class="bg-white border rounded-lg px-6 py-4 mb-6">
    <form method="GET" action="{{ url('/log-aktivitas') }}"
          class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">

        <div>
            <label class="text-sm font-medium block mb-1">Tanggal Awal</label>
            <input type="date"
                   name="tanggal_awal"
                   value="{{ request('tanggal_awal') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="text-sm font-medium block mb-1">Tanggal Akhir</label>
            <input type="date"
                   name="tanggal_akhir"
                   value="{{ request('tanggal_akhir') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <button type="submit"
                    class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                Tampilkan
            </button>
        </div>

        <div>
            <a href="{{ url('/log-aktivitas') }}"
               class="block text-center w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- ================= TABEL LOG ================= --}}
<div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full border-collapse">
        <thead class="bg-gray-100">
            <tr class="text-left text-sm font-semibold text-gray-700">
                <th class="px-4 py-3 border">No</th>
                <th class="px-4 py-3 border">User</th>
                <th class="px-4 py-3 border">Aksi</th>
                <th class="px-4 py-3 border">Waktu</th>
            </tr>
        </thead>
        <tbody class="text-sm">

            @forelse ($logAktivitas as $index => $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border">
                        {{ $logAktivitas->firstItem() + $index }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $log->user->name ?? 'System' }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $log->aksi }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $log->created_at->format('d-m-Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4"
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

@endsection
