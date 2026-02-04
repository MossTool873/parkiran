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



{{-- ===================== STRUK KELUAR (OVERLAY + PRINT) ===================== --}}
@if (session('struk_keluar'))
@php $s = session('struk_keluar'); @endphp

<div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow p-6 w-96 relative font-mono text-sm print-area">

        <div class="text-center font-bold mb-3">
            STRUK PARKIR KELUAR
        </div>

        <div class="space-y-1">
            <div>Plat      : {{ $s['plat'] }}</div>
            <div>Masuk     : {{ $s['jam_masuk'] }}</div>
            <div>Keluar    : {{ $s['jam_keluar'] }}</div>
            <div>Durasi    : {{ $s['durasi'] }}</div>
        </div>

        <hr class="my-3">

        <div class="space-y-1">
            <div>Tarif Awal : Rp {{ number_format($s['tarif'],0,',','.') }}</div>

            <div>
                Diskon ({{ $s['diskon_persen'] }}%) :
                - Rp {{ number_format($s['diskon'],0,',','.') }}
            </div>

            <div class="font-bold">
                TOTAL : Rp {{ number_format($s['total'],0,',','.') }}
            </div>

            <div>Metode : {{ $s['metode'] }}</div>
        </div>

        <hr class="my-3">

        <div class="text-xs">
            <div>Kode     : {{ $s['kode'] }}</div>
            <div>Tanggal  : {{ $s['tanggal'] }}</div>
            <div>Operator : {{ $s['operator'] }}</div>
        </div>

        <div class="text-center mt-2">
            Terima Kasih
        </div>

        <div class="mt-4 flex gap-2 no-print">
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

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
                    Simpan Masuk
                </button>
            </form>
        </div>

        {{-- ===================== KELUAR ===================== --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-xl font-semibold mb-6">Transaksi Kendaraan Keluar</h4>

            <form method="POST" action="{{ url('/petugas/transaksi/keluar') }}" id="formKeluar" class="space-y-4" onsubmit="return confirm('Anda Yakin?')">
                @csrf
                <input type="hidden" name="metode_pembayaran_id" id="metode_pembayaran_id">
                <div class="relative">
                    <label class="block text-sm font-medium mb-1">Plat Nomor</label>
                    <input type="text" name="plat_nomor" id="plat_keluar" value="{{ old('plat_nomor') }}"
                        class="w-full border rounded px-3 py-2" autocomplete="off" required>
                    <ul id="keluar-list"
                        class="absolute left-0 right-0 mt-1 bg-white border rounded shadow max-h-48 overflow-y-auto hidden z-50">
                    </ul>
                </div>

                <button type="button" onclick="openPembayaran()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded">
                    Proses Keluar
                </button>


            </form>
        </div>

    </div>
    {{-- ===================== METODE PEMBAYARAN ===================== --}}
    <div id="modalPembayaran" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-80">

            <h3 class="text-lg font-bold mb-4 text-center">
                Pilih Metode Pembayaran
            </h3>

            <div class="space-y-2">
                @foreach ($metodePembayarans as $metode)
                    <button type="button" onclick="submitKeluar({{ $metode->id }})"
                        class="w-full border rounded py-2 hover:bg-blue-600 hover:text-white">
                        {{ $metode->nama_metode }}
                    </button>
                @endforeach
            </div>

            <button onclick="closePembayaran()" class="mt-4 w-full bg-gray-300 hover:bg-gray-400 py-2 rounded">
                Batal
            </button>
        </div>
    </div>


    {{-- ===================== SCRIPT ===================== --}}
    <script>
        const platInput = document.getElementById('plat_nomor');
        const suggestions = document.getElementById('suggestions');
        const warnaInput = document.getElementById('warna');
        const tipeSelect = document.getElementById('tipe_kendaraan_id');

        platInput.addEventListener('keyup', function() {
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

        const platKeluar = document.getElementById('plat_keluar');
        const keluarList = document.getElementById('keluar-list');

        platKeluar.addEventListener('keyup', function() {
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
    </script>

    <script>
        function openPembayaran() {
            const modal = document.getElementById('modalPembayaran');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePembayaran() {
            const modal = document.getElementById('modalPembayaran');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function submitKeluar(metodeId) {
            document.getElementById('metode_pembayaran_id').value = metodeId;
            document.getElementById('formKeluar').submit();
        }
    </script>
@endsection
