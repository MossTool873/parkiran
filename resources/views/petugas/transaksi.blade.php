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

{{-- ===================== STRUK MASUK (OVERLAY + PRINT) ===================== --}}
@if (session('struk_masuk'))
@php $s = session('struk_masuk'); @endphp

<div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow p-6 w-96 relative font-mono text-sm print-area">

        {{-- HEADER --}}
        <div class="text-center font-bold mb-4">
            STRUK PARKIR MASUK
        </div>

        {{-- INFO --}}
        <div class="space-y-1">
            <div>Area      : {{ $s['area'] }}</div>
            <div>Jam Masuk : {{ $s['waktu'] }}</div>
            <div>Plat      : {{ $s['plat'] }}</div>
            <div>Tipe      : {{ $s['tipe'] }}</div>
        </div>

        <hr class="my-3">

        {{-- KODE --}}
        <div class="text-center">
            <div>Kode Transaksi</div>
            <div class="font-bold">{{ $s['kode'] }}</div>

            <canvas id="qr-struk" class="mx-auto mt-2"></canvas>
        </div>

        {{-- BUTTON --}}
        <div class="mt-5 flex gap-2 no-print">
            <button onclick="window.print()"
                class="flex-1 border px-3 py-1 rounded">
                Print
            </button>

            <button onclick="this.closest('.fixed').remove()"
                class="flex-1 border px-3 py-1 rounded">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
new QRious({
    element: document.getElementById('qr-struk'),
    value: "{{ $s['kode'] }}",
    size: 120
});
</script>
@endif






{{-- ===================== SCRIPT PRINT ===================== --}}
<script>
function basePrint(content) {
    const win = window.open('', '', 'width=400,height=600');
    win.document.write(`
        <html>
        <head>
            <title>Print Struk</title>
            <style>
                body {
                    font-family: monospace;
                    font-size: 12px;
                    margin: 0;
                    padding: 10px;
                }
                .font-bold { font-weight: bold; }
                .text-center { text-align: center; }
                .space-y-1 > div { margin-bottom: 4px; }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);
    win.document.close();
    win.focus();
    win.print();
    win.close();
}

function printStrukMasuk() {
    basePrint(document.getElementById('struk-masuk-print').innerHTML);
}

function printStrukKeluar() {
    basePrint(document.getElementById('struk-keluar-print').innerHTML);
}
</script>



    {{-- ===================== FORM MASUK & KELUAR ===================== --}}
    <div class="max-w-6xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- ===================== MASUK ===================== --}}
<div class="bg-white p-6 rounded-lg shadow">
    <h4 class="text-xl font-semibold mb-6">Transaksi Kendaraan Masuk</h4>

    <form method="POST" action="{{ url('/petugas/transaksi/masuk') }}" class="space-y-4" onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
        @csrf

        <div class="relative">
            <label class="block text-sm font-medium mb-1">Plat Nomor</label>
            <input type="text" name="plat_nomor" id="plat_nomor" value="{{ old('plat_nomor') }}"
                class="w-full border rounded px-3 py-2" autocomplete="off" required>
            <ul id="suggestions"
                class="absolute left-0 right-0 mt-1 bg-white border rounded shadow max-h-48 overflow-y-auto hidden z-50">
            </ul>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Warna</label>
            <input type="text" name="warna" id="warna" value="{{ old('warna') }}"
                class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
            <select name="tipe_kendaraan_id" id="tipe_kendaraan_id" class="w-full border rounded px-3 py-2"
                required>
                <option value="">-- Pilih --</option>
                @foreach ($tipeKendaraans as $tipe)
                    <option value="{{ $tipe->id }}"
                        {{ old('tipe_kendaraan_id') == $tipe->id ? 'selected' : '' }}>
                        {{ $tipe->tipe_kendaraan }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Area Parkir</label>
            <select name="area_parkir_id" id="area_parkir_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih --</option>
                @foreach ($areaParkirs as $area)
                    <option value="{{ $area->id }}"
                        {{ old('area_parkir_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->nama_area }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
            Simpan Masuk
        </button>
    </form>
</div>
<script>
    // =================== AUTOCOMPLETE KENDARAAN MASUK ===================
    const platInput = document.getElementById('plat_nomor');
    const suggestions = document.getElementById('suggestions');
    const warnaInput = document.getElementById('warna');
    const tipeSelect = document.getElementById('tipe_kendaraan_id');

    platInput.addEventListener('keyup', function() {
        const q = this.value.trim();
        if (!q) return suggestions.classList.add('hidden');

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


</script>

    {{-- ===================== TRANSAKSI KELUAR ===================== --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h4 class="text-xl font-semibold mb-6">Transaksi Kendaraan Keluar</h4>


        {{-- Form input plat/kode --}}
        <form method="POST" action="{{ route('keluar.hitung') }}" class="space-y-4">
            @csrf
            <div class="relative">
                <label class="block text-sm font-medium mb-1">Plat Nomor / Kode Transaksi</label>
                <input type="text" id="plat_keluar" name="plat_nomor"
                       class="w-full border rounded px-3 py-2"
                       autocomplete="off"
                       placeholder="Masukkan Plat Nomor atau Kode"
                       value="{{ old('plat_nomor') }}" required>

                {{-- List autocomplete --}}
                <ul id="keluar-list"
                    class="absolute left-0 right-0 mt-1 bg-white border rounded shadow max-h-48 overflow-y-auto hidden z-50">
                </ul>
            </div>

            <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded">
                Cek & Hitung Biaya
            </button>
        </form>
    </div>

{{-- ===================== OVERLAY BAYAR ===================== --}}
@if(session('draft_keluar'))
    @php $s = session('draft_keluar'); @endphp
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded shadow p-6 w-96 relative font-mono text-sm">

            {{-- Tombol batal overlay --}}
            <form method="POST" action="{{ route('keluar.batal') }}" class="absolute top-3 right-3">
                @csrf
                <button class="w-8 h-8 flex items-center justify-center
                               rounded-full
                               text-lg font-bold
                               text-gray-500
                               hover:text-white hover:bg-red-600 transition">
                    &times;
                </button>
            </form>

            <h3 class="text-center font-bold mb-3">KONFIRMASI PEMBAYARAN</h3>

            <div class="space-y-1">
                <div>Plat       : {{ $s['plat'] }}</div>
                <div>Jam Masuk  : {{ $s['jam_masuk'] }}</div>
                <div>Jam Keluar : {{ $s['jam_keluar'] }}</div>
                <div>Durasi     : {{ $s['durasi'] }}</div>
                <div>Member     : {{ $s['member'] }}</div>
            </div>

            <hr class="my-3">

            <div class="space-y-1">
                <div>Biaya Awal         : Rp {{ number_format($s['biaya_awal'],0,',','.') }}</div>
                <div>Diskon             : - Rp {{ number_format($s['diskon_non_member'],0,',','.') }}
                    ({{ $s['diskon_non_member_persen'] }}%)
                </div>
                @if($s['diskon_member'] > 0)
                    <div>Diskon Member      : - Rp {{ number_format($s['diskon_member'],0,',','.') }}
                        ({{ $s['diskon_member_persen'] }}%)
                    </div>
                @endif
                <div class="font-bold">TOTAL              : Rp {{ number_format($s['total'],0,',','.') }}</div>
            </div>

            <hr class="my-3">

            {{-- Pilih Metode Bayar --}}
            <form method="POST" action="{{ route('keluar.bayar') }}">
                @csrf
                <label class="block text-sm font-medium mb-1">Metode Pembayaran</label>
                <select name="metode_pembayaran_id" class="w-full border px-3 py-2 mb-4" required>
                    <option value="">-- Pilih --</option>
                    @foreach($metodePembayarans as $metode)
                        <option value="{{ $metode->id }}">{{ $metode->nama_metode }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded">
                    Bayar
                </button>
            </form>
        </div>
    </div>
@endif


{{-- ===================== OVERLAY STRUK ===================== --}}
@if(session('struk_keluar'))
    @php $s = session('struk_keluar'); @endphp
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded shadow p-6 w-96 relative font-mono text-sm print-area">

            {{-- Tombol batal/tutup --}}
            <form method="POST" action="{{ route('keluar.batal') }}" class="absolute top-3 right-3 no-print">
                @csrf
                <button
                    type="submit"
                    class="w-10 h-10 flex items-center justify-center
                           rounded-full
                           text-2xl font-bold
                           text-gray-500
                           hover:text-white hover:bg-red-600
                           transition">
                    &times;
                </button>
            </form>

            {{-- HEADER --}}
            <div class="text-center font-bold mb-3">
                STRUK PARKIR KELUAR
            </div>

            {{-- INFO KENDARAAN --}}
            <div class="space-y-1">
                <div>Plat      : {{ $s['plat'] }}</div>
                <div>Masuk     : {{ $s['jam_masuk'] }}</div>
                <div>Keluar    : {{ $s['jam_keluar'] }}</div>
                <div>Durasi    : {{ $s['durasi'] }}</div>
                <div>Member    : {{ $s['member'] }}</div>
            </div>

            <hr class="my-3">

            {{-- BIAYA --}}
            <div class="space-y-1">
                <div>Biaya Awal         : Rp {{ number_format($s['biaya_awal'], 0, ',', '.') }}</div>
                <div>Diskon  : - Rp {{ number_format($s['diskon_non_member'],0,',','.') }}
                    ({{ $s['diskon_non_member_persen'] }}%)
                </div>
                @if($s['diskon_member'] > 0)
                    <div>Diskon Member      : - Rp {{ number_format($s['diskon_member'],0,',','.') }}
                        ({{ $s['diskon_member_persen'] }}%)
                    </div>
                @endif
                <div class="font-bold">TOTAL              : Rp {{ number_format($s['total'],0,',','.') }}</div>
                <div>Metode : {{ $s['metode'] }}</div>
            </div>

            <hr class="my-3">

            {{-- META --}}
            <div class="text-xs">
                <div>Kode     : {{ $s['kode'] }}</div>
                <div>Tanggal  : {{ $s['tanggal'] }}</div>
                <div>Operator : {{ $s['operator'] }}</div>
            </div>

            <div class="text-center mt-2">
                Terima Kasih
            </div>

            {{-- BUTTON --}}
            <div class="mt-4 flex gap-2 no-print">
                <button onclick="window.print()"
                    class="flex-1 border px-3 py-1 rounded">
                    Print
                </button>

                <form method="POST" action="{{ route('keluar.batal') }}" class="flex-1">
                    @csrf
                    <button class="w-full border px-3 py-1 rounded bg-gray-500 hover:bg-gray-600 text-white">
                        Tutup
                    </button>
                </form>
            </div>

        </div>
    </div>
@endif



{{-- ===================== SCRIPT AUTOCOMPLETE ===================== --}}
<script>
const platKeluar = document.getElementById('plat_keluar');
const keluarList = document.getElementById('keluar-list');

platKeluar?.addEventListener('keyup', function() {
    const q = this.value.trim();
    if(!q) return keluarList.classList.add('hidden');

    fetch(`{{ route('transaksi.transaksiAktif') }}?q=${q}`)
        .then(res => res.json())
        .then(data => {
            keluarList.innerHTML = '';
            if(!data.length) return keluarList.classList.add('hidden');
            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.plat_nomor + (item.kode ? ' / ' + item.kode : '');
                li.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
                li.onclick = () => { platKeluar.value = item.plat_nomor; keluarList.classList.add('hidden'); };
                keluarList.appendChild(li);
            });
            keluarList.classList.remove('hidden');
        });
});

document.addEventListener('click', e=>{
    if(!platKeluar?.contains(e.target) && !keluarList?.contains(e.target)) keluarList.classList.add('hidden');
});
</script>

@endsection