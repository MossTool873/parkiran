@extends('components.app')

@section('title', 'Form Membership')

@section('content')
<h1 class="text-2xl font-bold mb-6">
    {{ isset($membership) ? 'Edit Membership' : 'Tambah Membership' }}
</h1>

@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
<form method="POST"
      action="{{ isset($membership) ? route('membership.update',$membership->id) : route('membership.store') }}">
    @csrf
    @isset($membership)
        @method('PUT')
    @endisset

    {{-- ================= BASIC ================= --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div>
        <label class="text-sm font-medium">Nama</label>
        <input type="text"
               name="nama"
               value="{{ old('nama', $membership->nama ?? '') }}"
               class="w-full border rounded px-3 py-2"
               required>
    </div>

    <div>
        <label class="text-sm font-medium">Tier</label>
        <select name="membership_tier_id"
                class="w-full border rounded px-3 py-2"
                required>
            <option value="">-- Pilih Tier --</option>
            @foreach($tiers as $tier)
                <option value="{{ $tier->id }}"
                    {{ old('membership_tier_id', $membership->membership_tier_id ?? '') == $tier->id ? 'selected' : '' }}>
                    {{ $tier->membership_tier }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- KADALUARSA --}}
    <div>
        <label class="text-sm font-medium">Kadaluarsa</label>
        <input type="date"
               name="kadaluarsa"
               value="{{ old('kadaluarsa',
                    isset($membership)
                        ? \Carbon\Carbon::parse($membership->kadaluarsa)->format('Y-m-d')
                        : ''
               ) }}"
               class="w-full border rounded px-3 py-2"
               required>
    </div>
</div>


    {{-- ================= KENDARAAN ================= --}}
    <h2 class="font-semibold mb-2">Kendaraan</h2>

    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
        <tr>
            <th class="px-3 py-2">Plat</th>
            <th class="px-3 py-2">Warna</th>
            <th class="px-3 py-2">Tipe</th>
            <th class="px-3 py-2 text-center">Aksi</th>
        </tr>
        </thead>
        <tbody id="kendaraan-body">

        {{-- DEFAULT ROW --}}
        <tr>
            <td class="relative px-2 py-2">
                <input type="text" name="kendaraan[0][plat_nomor]"
                       class="plat-input w-full border rounded px-2 py-1">
                <ul class="suggestions hidden absolute bg-white border rounded shadow w-full z-10"></ul>
            </td>
            <td class="px-2 py-2">
                <input type="text" name="kendaraan[0][warna]"
                       class="warna-input w-full border rounded px-2 py-1">
            </td>
            <td class="px-2 py-2">
                <select name="kendaraan[0][tipe_kendaraan_id]"
                        class="tipe-input w-full border rounded px-2 py-1">
                    <option value="">-- Pilih --</option>
                    @foreach($tipeKendaraans as $tipe)
                        <option value="{{ $tipe->id }}">{{ $tipe->tipe_kendaraan }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-2 py-2 text-center">
                <button type="button" class="hapus-row text-red-600">Hapus</button>
            </td>
        </tr>

        </tbody>
    </table>

    <button type="button"
            id="tambah-row"
            class="mt-3 bg-green-600 text-white px-3 py-1 rounded">
        + Tambah Kendaraan
    </button>

    {{-- ================= ACTION ================= --}}
    <div class="mt-6 flex gap-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Simpan
        </button>
        <a href="{{ route('membership.index') }}"
           class="bg-gray-300 px-4 py-2 rounded">
            Batal
        </a>
    </div>
</form>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
let index = 1;

/* ===================== TAMBAH ROW ===================== */
document.getElementById('tambah-row').onclick = () => {
    const body = document.getElementById('kendaraan-body');
    const row = body.rows[0].cloneNode(true);

    row.querySelectorAll('input, select').forEach(el => {
        el.value = '';
        el.readOnly = false;
        el.style.pointerEvents = 'auto';
        el.classList.remove('bg-gray-100');

        el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
    });

    const list = row.querySelector('.suggestions');
    list.innerHTML = '';
    list.classList.add('hidden');

    body.appendChild(row);
    index++;
};

/* ===================== HAPUS ROW (MINIMAL 1) ===================== */
document.addEventListener('click', e => {
    if (e.target.classList.contains('hapus-row')) {
        const body = document.getElementById('kendaraan-body');
        if (body.rows.length > 1) {
            e.target.closest('tr').remove();
        }
    }
});

/* ===================== AUTOCOMPLETE PLAT ===================== */
document.addEventListener('input', e => {
    if (!e.target.classList.contains('plat-input')) return;

    const row   = e.target.closest('tr');
    const warna = row.querySelector('.warna-input');
    const tipe  = row.querySelector('.tipe-input');
    const list  = row.querySelector('.suggestions');
    const q     = e.target.value.trim();

    if (!q) {
        list.classList.add('hidden');
        return;
    }

    fetch(`{{ route('kendaraan.search') }}?q=${q}`)
        .then(r => r.json())
        .then(data => {
            list.innerHTML = '';

            /* ===== JIKA TIDAK ADA DATA (INPUT MANUAL) ===== */
            if (!data.length) {
                warna.readOnly = false;
                tipe.style.pointerEvents = 'auto';
                tipe.classList.remove('bg-gray-100');
                list.classList.add('hidden');
                return;
            }

            /* ===== ADA DATA (AUTOFILL) ===== */
            data.forEach(k => {
                const li = document.createElement('li');
                li.textContent = k.plat_nomor;
                li.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';

                li.onclick = () => {
                    e.target.value = k.plat_nomor;
                    warna.value = k.warna;
                    tipe.value = k.tipe_kendaraan_id;

                    /* KUNCI INPUT (TAPI TETAP TERKIRIM) */
                    warna.readOnly = true;
                    tipe.style.pointerEvents = 'none';
                    tipe.classList.add('bg-gray-100');

                    list.classList.add('hidden');
                };

                list.appendChild(li);
            });

            list.classList.remove('hidden');
        });
});
</script>

@endsection
