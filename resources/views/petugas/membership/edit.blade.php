@extends('components.app')

@section('title', 'Edit Membership Petugas')

@section('content')
<div class="max-w-6xl mx-auto mt-8">

    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-4 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('petugas.membership.update', $membership->id) }}" method="POST" onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded shadow">

            <h2 class="text-lg font-semibold mb-4">Edit Membership</h2>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block mb-1">Nama Membership</label>
                    <input type="text"
                           name="nama"
                           value="{{ old('nama', $membership->nama) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1">Tier Membership</label>
                    <select name="membership_tier_id"
                            class="w-full border rounded px-3 py-2">
                        @foreach ($tiers as $tier)
                            <option value="{{ $tier->id }}"
                                {{ old('membership_tier_id', $membership->membership_tier_id) == $tier->id ? 'selected' : '' }}>
                                {{ $tier->membership_tier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Kadaluarsa</label>
                    <input type="date"
                           name="kadaluarsa"
                           value="{{ old('kadaluarsa', $membership->kadaluarsa) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <h3 class="font-semibold mb-2">Kendaraan</h3>

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

                @foreach ($membership->kendaraans as $index => $kendaraan)
                    <tr>
                        <td class="relative px-2 py-2">
                            <input type="text"
                                   name="kendaraan[{{ $index }}][plat_nomor]"
                                   value="{{ $kendaraan->plat_nomor }}"
                                   class="plat-input w-full border rounded px-2 py-1">
                            <ul class="suggestions hidden absolute bg-white border rounded shadow w-full z-10"></ul>
                        </td>

                        <td class="px-2 py-2">
                            <input type="text"
                                   name="kendaraan[{{ $index }}][warna]"
                                   value="{{ $kendaraan->warna }}"
                                   class="warna-input w-full border rounded px-2 py-1">
                        </td>

                        <td class="px-2 py-2">
                            <select name="kendaraan[{{ $index }}][tipe_kendaraan_id]"
                                    class="tipe-input w-full border rounded px-2 py-1">
                                @foreach ($tipeKendaraans as $tipe)
                                    <option value="{{ $tipe->id }}"
                                        {{ $kendaraan->tipe_kendaraan_id == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->tipe_kendaraan }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="px-2 py-2 text-center">
                            <button type="button" class="hapus-row text-red-600">Hapus</button>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

            <button type="button"
                    id="tambah-row"
                    class="mt-3 bg-green-600 text-white px-3 py-1 rounded">
                + Tambah Kendaraan
            </button>

            <div class="mt-6 flex gap-2">
                <a href="{{ route('petugas.membership.index') }}"
                   class="px-4 py-2 bg-gray-300 rounded">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                    Update
                </button>
            </div>

        </div>
    </form>
</div>

<script>
let index = {{ $membership->kendaraans->count() }};

/* TAMBAH ROW */
document.getElementById('tambah-row').onclick = () => {
    const body = document.getElementById('kendaraan-body');
    const row  = body.rows[0].cloneNode(true);

    row.querySelectorAll('input, select').forEach(el => {
        el.value = '';
        el.readOnly = false;
        el.style.pointerEvents = 'auto';
        el.classList.remove('bg-gray-100');
        el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
    });

    row.querySelector('.suggestions').innerHTML = '';
    row.querySelector('.suggestions').classList.add('hidden');

    body.appendChild(row);
    index++;
};

/* HAPUS ROW */
document.addEventListener('click', e => {
    if (e.target.classList.contains('hapus-row')) {
        const body = document.getElementById('kendaraan-body');
        if (body.rows.length > 1) e.target.closest('tr').remove();
    }
});

/* AUTOCOMPLETE PLAT */
document.addEventListener('input', e => {
    if (!e.target.classList.contains('plat-input')) return;
    const row = e.target.closest('tr');
    const warna = row.querySelector('.warna-input');
    const tipe  = row.querySelector('.tipe-input');
    const list  = row.querySelector('.suggestions');
    const q     = e.target.value.trim();
    if (!q) { list.classList.add('hidden'); return; }

    fetch(`{{ route('kendaraan.search') }}?q=${q}`)
        .then(r => r.json())
        .then(data => {
            list.innerHTML = '';
            if (!data.length) {
                warna.readOnly = false;
                tipe.style.pointerEvents = 'auto';
                tipe.classList.remove('bg-gray-100');
                list.classList.add('hidden');
                return;
            }

            data.forEach(k => {
                const li = document.createElement('li');
                li.textContent = k.plat_nomor;
                li.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
                li.onclick = () => {
                    e.target.value = k.plat_nomor;
                    warna.value = k.warna;
                    tipe.value  = k.tipe_kendaraan_id;
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
