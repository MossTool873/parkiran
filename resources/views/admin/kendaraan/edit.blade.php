@extends('components.app')

@section('title', 'Edit Kendaraan')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Kendaraan</h1>

<div class="bg-white rounded-lg shadow p-6 w-full">
    <form action="{{ url('/admin/kendaraan/'.$kendaraan->id) }}"
          method="POST"
          class="space-y-4"
          onsubmit="return validatePlat()">
        @csrf
        @method('PUT')

        {{-- PLAT NOMOR --}}
        <div>
            <label class="block text-sm font-medium mb-1">Plat Nomor</label>
            <input type="text"
                   name="plat_nomor"
                   id="plat_nomor"
                   placeholder="A 1234 BU"
                   maxlength="11"
                   autocomplete="off"
                   class="w-full border rounded-lg px-3 py-2 uppercase tracking-widest"
                   value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}"
                   required>

            <p class="text-xs text-gray-500 mt-1">
                Format: A 1234 BU
            </p>

            @error('plat_nomor')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- WARNA --}}
        <div>
            <label class="block text-sm font-medium mb-1">Warna</label>
            <input type="text"
                   name="warna"
                   class="w-full border rounded-lg px-3 py-2"
                   value="{{ old('warna', $kendaraan->warna) }}"
                   required>
            @error('warna')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- TIPE --}}
        <div>
            <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
            <select name="tipe_kendaraan_id"
                    class="w-full border rounded-lg px-3 py-2"
                    required>
                @foreach ($tipeKendaraans as $tipe)
                    <option value="{{ $tipe->id }}"
                        {{ old('tipe_kendaraan_id', $kendaraan->tipe_kendaraan_id) == $tipe->id ? 'selected' : '' }}>
                        {{ $tipe->tipe_kendaraan }}
                    </option>
                @endforeach
            </select>
            @error('tipe_kendaraan_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION --}}
        <div class="flex gap-2 pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update
            </button>
            <a href="{{ url('/admin/kendaraan') }}"
               class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                Batal
            </a>
        </div>
    </form>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
const platInput = document.getElementById('plat_nomor');

/* ================= FORMAT PLAT ================= */
platInput.addEventListener('input', function (e) {

    let raw = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    let hasil = '';

    let depan = raw.match(/^[A-Z]{1,2}/);
    if (!depan) {
        e.target.value = '';
        return;
    }

    depan = depan[0];
    hasil += depan + ' ';
    raw = raw.slice(depan.length);

    let angka = raw.match(/^[0-9]{0,4}/)[0];
    hasil += angka;
    raw = raw.slice(angka.length);

    if (angka.length > 0) {
        let belakang = raw.replace(/[^A-Z]/g, '').slice(0, 3);
        if (belakang.length > 0) {
            hasil += ' ' + belakang;
        }
    }

    e.target.value = hasil.trim();
});

/* ================= VALIDASI SUBMIT ================= */
function validatePlat() {
    const value = platInput.value.trim();
    // Format: 1-2 huruf + spasi + 1-4 angka + optional spasi + 0-3 huruf
    const regex = /^[A-Z]{1,2} [0-9]{1,4}( [A-Z]{1,3})?$/;

    if (!regex.test(value)) {
        alert('Format Plat nomor salah\nContoh: A 1 atau A 1234 BU');
        platInput.focus();
        return false;
    }

    return confirm('Yakin data yang dimasukkan sudah benar?');
}

</script>
@endsection
