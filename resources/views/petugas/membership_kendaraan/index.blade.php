@extends('components.app')

@section('title', 'Daftar Membership Kendaraan')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Membership Kendaraan</h1>

        {{-- Form Search di samping judul --}}
        <form method="GET" action="{{ url('/petugas/membership-kendaraan') }}" class="flex gap-2">
            <input type="text" name="search" placeholder="Cari Nama Member..."
                   class="border px-3 py-2 rounded-lg"
                   value="{{ $search ?? '' }}">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Cari
            </button>
        </form>
    </div>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table data --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Member</th>
                    <th class="px-4 py-3">Tipe Kendaraan</th>
                    <th class="px-4 py-3">Plat Nomor</th>
                    <th class="px-4 py-3">Warna</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($membershipKendaraans as $item)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration + ($membershipKendaraans->currentPage()-1)*$membershipKendaraans->perPage() }}</td>
                        <td class="px-4 py-3">{{ $item->membership ? $item->membership->nama : '-' }}</td>
                        <td class="px-4 py-3">{{ $item->kendaraan ? $item->kendaraan->tipeKendaraan->tipe_kendaraan : '-' }}</td>
                        <td class="px-4 py-3">{{ $item->kendaraan ? $item->kendaraan->plat_nomor : '-' }}</td>
                        <td class="px-4 py-3">{{ $item->kendaraan ? $item->kendaraan->warna : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Data membership kendaraan kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $membershipKendaraans->links() }}
    </div>
@endsection
