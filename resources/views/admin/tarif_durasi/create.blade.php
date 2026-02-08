@extends('components.app')

@section('title', 'Tambah Tarif Durasi')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-lg font-semibold mb-4">Tambah Tarif Durasi</h2>

    <form method="POST" action="{{ url('/admin/tarif-durasi') }}">
        @csrf

        {{-- Batas Jam --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">
                Batas Jam
            </label>
            <input
                type="number"
                name="batas_jam"
                min="1"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        {{-- Persentase --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">
                Persentase (%)
            </label>
            <input
                type="number"
                id="persentase"
                name="persentase"
                min="1"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        <hr class="my-6">

        {{-- PREVIEW --}}
        <h3 class="font-semibold mb-2">Preview Tarif</h3>

        <div class="overflow-x-auto">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">
                            Tipe Kendaraan
                        </th>
                        <th class="border px-3 py-2 text-right">
                            Tarif Dasar / Jam
                        </th>
                        <th class="border px-3 py-2 text-right">
                            Hasil Tarif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tarifDasar as $item)
                        <tr>
                            <td class="border px-3 py-2">
                                {{ $item->tipeKendaraan->tipe_kendaraan }}
                            </td>
                            <td
                                class="border px-3 py-2 text-right tarif-dasar"
                                data-tarif="{{ $item->tarif_perjam }}"
                            >
                                Rp {{ number_format($item->tarif_perjam, 0, ',', '.') }}
                            </td>
                            <td class="border px-3 py-2 text-right hasil">
                                -
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Simpan
            </button>

            <a href="{{ url('/admin/tarif-durasi') }}"
               class="px-4 py-2 bg-gray-300 rounded">
                Kembali
            </a>
        </div>
    </form>
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
