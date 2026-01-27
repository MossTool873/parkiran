@extends('components.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h4 class="text-xl font-semibold mb-6">Transaksi Kendaraan Masuk</h4>

    <form method="POST" action="{{ url('/petugas/transaksi/') }}" class="space-y-4">
        @csrf

        {{-- PLAT NOMOR --}}
        <div class="relative">
            <label class="block text-sm font-medium mb-1">Plat Nomor</label>
            <input
                type="text"
                name="plat_nomor"
                id="plat_nomor"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
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
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                required
            >
        </div>

        {{-- TIPE KENDARAAN --}}
        <div>
            <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
            <select
                name="tipe_kendaraan_id"
                id="tipe_kendaraan_id"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                required
            >
                <option value="">-- Pilih --</option>
                @foreach ($tipeKendaraans as $tipe)
                    <option value="{{ $tipe->id }}">{{ $tipe->tipe_kendaraan }}</option>
                @endforeach
            </select>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
        >
            Simpan
        </button>
    </form>
</div>

<script>
const platInput = document.getElementById('plat_nomor');
const suggestions = document.getElementById('suggestions');
const warnaInput = document.getElementById('warna');
const tipeSelect = document.getElementById('tipe_kendaraan_id');

platInput.addEventListener('keyup', function () {
    const q = this.value;

    if (q.length < 2) {
        suggestions.classList.add('hidden');
        return;
    }

    fetch(`{{ route('kendaraan.search') }}?q=${q}`)
        .then(res => res.json())
        .then(data => {
            suggestions.innerHTML = '';

            if (data.length === 0) {
                suggestions.classList.add('hidden');
                return;
            }

            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.plat_nomor;
                li.className = 'px-3 py-2 cursor-pointer hover:bg-gray-100';

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

document.addEventListener('click', function (e) {
    if (!suggestions.contains(e.target) && e.target !== platInput) {
        suggestions.classList.add('hidden');
    }
});
</script>
@endsection