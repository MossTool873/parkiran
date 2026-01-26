@extends('components.app')

@section('title', 'Tarif Tipe Kendaraan')

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Tarif Tipe Kendaraan</h1>

        <a href="{{ url('/admin/tarifTipeKendaraan/create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Tarif
        </a>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3 w-16">No</th>
                    <th class="px-4 py-3">Tipe Kendaraan</th>
                    <th class="px-4 py-3">Tarif / Jam</th>
                    <th class="px-4 py-3 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tarifTipeKendaraans as $tarif)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            {{ $tarif->tipeKendaraan->tipe_kendaraan }}
                        </td>
                        <td class="px-4 py-3">
                            Rp {{ number_format($tarif->tarif_perjam, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ url('/admin/tarifTipeKendaraan/'.$tarif->id.'/edit') }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ url('/admin/tarifTipeKendaraan/'.$tarif->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus tarif ini?')">
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
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            Data tarif kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection