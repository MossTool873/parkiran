@extends('components.app')

@section('content')

{{-- ===================== ERROR & SUCCESS ===================== --}}
@if ($errors->any())
    <div class="max-w-6xl mx-auto mt-6 bg-red-100 text-red-700 p-4 rounded">
        {{ $errors->first() }}
    </div>
@endif

@if (session('success'))
    <div class="max-w-6xl mx-auto mt-6 bg-green-100 text-green-700 p-4 rounded">
        {{ session('success') }}
    </div>
@endif

<div class="max-w-6xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ===================== MASUK ===================== --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h4 class="text-xl font-semibold mb-6">Transaksi Kendaraan Masuk</h4>

        <form method="POST" action="{{ url('/petugas/transaksi/masuk') }}" class="space-y-4">
            @csrf

            {{-- PLAT NOMOR --}}
            <div class="relative">
                <label class="block text-sm font-medium mb-1">Plat Nomor</label>
                <input
                    type="text"
                    name="plat_nomor"
                    id="plat_nomor"
                    value="{{ old('plat_nomor') }}"
                    class="w-full border rounded px-3 py-2"
                    autocomplete="off"
                    required
                >
                <ul
                    id="suggestions"
                    class="absolute left-0 right-0 mt-1 bg-white border rounded shadow max-h-48 overflow-y-auto hidden z-50"
                ></ul>
            </div>

            {{-- WARNA --}}
            <div>
                <label class="block text-sm font-medium mb-1">Warna</label>
                <input
                    type="text"
                    name="warna"
                    id="warna"
                    value="{{ old('warna') }}"
                    class="w-full border rounded px-3 py-2"
                    required
                >
            </div>

            {{-- TIPE --}}
            <div>
                <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
                <select
                    name="tipe_kendaraan_id"
                    id="tipe_kendaraan_id"
                    class="w-full border rounded px-3 py-2"
                    required
                >
                    <option value="">-- Pilih --</option>
                    @foreach ($tipeKendaraans as $tipe)
                        <option
                            value="{{ $tipe->id }}"
                            {{ old('tipe_kendaraan_id') == $tipe->id ? 'selected' : '' }}
                        >
                            {{ $tipe->tipe_kendaraan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="w-full bg-blue-600 text-white py-2 rounded">
                Simpan Masuk
            </button>
        </form>
    </div>

    {{-- ===================== KELUAR ===================== --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h4 class="text-xl font-semibold mb-6">Transaksi Kendaraan Keluar</h4>

        <form method="POST" action="{{ url('/petugas/transaksi/keluar') }}" class="space-y-4">
            @csrf

            <div class="relative">
                <label class="block text-sm font-medium mb-1">Plat Nomor</label>
                <input
                    type="text"
                    name="plat_nomor"
                    id="plat_keluar"
                    value="{{ old('plat_nomor') }}"
                    class="w-full border rounded px-3 py-2"
                    autocomplete="off"
                    required
                >

                <ul
                    id="keluar-list"
                    class="absolute left-0 right-0 mt-1 bg-white border rounded shadow max-h-48 overflow-y-auto hidden z-50"
                ></ul>
            </div>

            <button class="w-full bg-red-600 text-white py-2 rounded">
                Proses Keluar
            </button>
        </form>
    </div>

</div>

{{-- ===================== SCRIPT ===================== --}}
<script>
// ===== MASUK (autocomplete kendaraan lama) =====
const platInput = document.getElementById('plat_nomor');
const suggestions = document.getElementById('suggestions');
const warnaInput = document.getElementById('warna');
const tipeSelect = document.getElementById('tipe_kendaraan_id');

platInput.addEventListener('keyup', function () {
    const q = this.value;
    if (q.length < 1) return suggestions.classList.add('hidden');

    fetch(`{{ route('kendaraan.search') }}?q=${q}`)
        .then(res => res.json())
        .then(data => {
            suggestions.innerHTML = '';
            if (!data.length) return suggestions.classList.add('hidden');

            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.plat_nomor;
                li.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
                li.onclick = () => {
                    platInput.value = item.plat_nomor;
                    warnaInput.value = item.warna;
                    tipeSelect.value = item.tipe_kendaraan_id;
                    suggestions.classList.add('hidden');
                };
                suggestions.appendChild(li);
            });
            suggestions.classList.remove('hidden');
        });
});

// ===== KELUAR (hanya transaksi aktif) =====
const platKeluar = document.getElementById('plat_keluar');
const keluarList = document.getElementById('keluar-list');

platKeluar.addEventListener('keyup', function () {
    const q = this.value;
    if (q.length < 1) return keluarList.classList.add('hidden');

    fetch(`{{ route('transaksi.aktif') }}?q=${q}`)
        .then(res => res.json())
        .then(data => {
            keluarList.innerHTML = '';
            if (!data.length) return keluarList.classList.add('hidden');

            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.plat_nomor + ' • ' + item.waktu_masuk;
                li.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
                li.onclick = () => {
                    platKeluar.value = item.plat_nomor;
                    keluarList.classList.add('hidden');
                };
                keluarList.appendChild(li);
            });
            keluarList.classList.remove('hidden');
        });
});

document.addEventListener('click', e => {
    if (!suggestions.contains(e.target)) suggestions.classList.add('hidden');
    if (!keluarList.contains(e.target)) keluarList.classList.add('hidden');
});
</script>
@endsection