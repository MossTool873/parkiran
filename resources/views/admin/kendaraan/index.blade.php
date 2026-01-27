@extends('components.app')

@section('title', 'Data Kendaraan')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Kendaraan</h1>

        <a href="{{ url('/admin/kendaraan/create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Kendaraan
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3 w-16">No</th>
                    <th class="px-4 py-3">Plat Nomor</th>
                    <th class="px-4 py-3">Warna</th>
                    <th class="px-4 py-3">Tipe Kendaraan</th>
                    <th class="px-4 py-3 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kendaraans as $kendaraan)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $kendaraan->plat_nomor }}</td>
                        <td class="px-4 py-3">{{ $kendaraan->warna }}</td>
                        <td class="px-4 py-3">
                            {{ $kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ url('/admin/kendaraan/'.$kendaraan->id.'/edit') }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ url('/admin/kendaraan/'.$kendaraan->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Data kendaraan kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection