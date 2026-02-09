@extends('components.app')

@section('title', 'Tambah Tarif Durasi')

@section('content')
<div class="w-full px-6 py-6">

    {{-- ================= HEADER ================= --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Tambah Tarif Durasi</h1>
        <p class="text-sm text-gray-500">
            Tambahkan tarif tambahan berdasarkan durasi
        </p>
    </div>

    {{-- ================= ALERT ================= --}}
    @if (session('success'))
        <div class="mb-6 bg-green-100 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- ================= GRID KIRI KANAN ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        {{-- ================= CARD KIRI : FORM ================= --}}
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ url('/admin/tarif-durasi') }}" class="space-y-6" onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
                @csrf

                {{-- Batas Jam --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Batas Jam
                    </label>
                    <input
                        type="number"
                        name="batas_jam"
                        min="1"
                        class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                        required
                    >
                </div>

                {{-- Persentase --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Persentase (%)
                    </label>
                    <input
                        type="number"
                        id="persentase"
                        name="persentase"
                        min="1"
                        class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                        required
                    >
                </div>

                {{-- SUBMIT --}}
                <div class="flex justify-end gap-2">
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Simpan
                    </button>

                    <a href="{{ url('/admin/tarif-durasi') }}"
                       class="px-6 py-2 bg-gray-300 rounded-lg">
                        Kembali
                    </a>
                </div>
            </form>
        </div>

        {{-- ================= CARD KANAN : ESTIMASI ================= --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Preview Tarif</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-2">Tipe Kendaraan</th>
                            <th class="px-4 py-2 text-right">Tarif Dasar / Jam</th>
                            <th class="px-4 py-2 text-right">Hasil Tarif</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($tarifDasar as $item)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2 font-medium">
                                    {{ $item->tipeKendaraan->tipe_kendaraan }}
                                </td>

                                <td
                                    class="px-4 py-2 text-right tarif-dasar"
                                    data-tarif="{{ $item->tarif_perjam }}"
                                >
                                    Rp {{ number_format($item->tarif_perjam, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-2 text-right hasil">
                                    -
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-xs text-gray-500">
                Preview otomatis menghitung tarif tambahan berdasarkan persentase yang diisi.
            </p>
        </div>

    </div>
</div>

<script>
document.getElementById('persentase').addEventListener('input', function () {
    const persen = parseFloat(this.value);

    document.querySelectorAll('tbody tr').forEach(row => {
        const tarif = parseFloat(
            row.querySelector('.tarif-dasar').dataset.tarif
        );

        if (!isNaN(persen)) {
            const hasil = tarif * persen / 100;
            row.querySelector('.hasil').innerText =
                'Rp ' + hasil.toLocaleString('id-ID');
        } else {
            row.querySelector('.hasil').innerText = '-';
        }
    });
});
</script>
@endsection
