@extends('components.app')

@section('title', 'Manajemen Membership')

@section('content')
    {{-- HEADER: Judul & Tombol Tambah --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4">
        <h1 class="text-2xl font-bold">Manajemen Membership</h1>

        <a href="{{ route('membership.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Membership
        </a>
    </div>

    {{-- SEARCH BAR --}}
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ route('membership.index') }}">
            <div class="flex gap-2 w-full md:w-80">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama membership..."
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    Cari
                </button>
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE MEMBERSHIP --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Tier</th>
                    <th class="px-4 py-3">Pembaruan Terakhir</th>
                    <th class="px-4 py-3">Kadaluarsa</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($memberships as $membership)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ ($memberships->currentPage() - 1) * $memberships->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $membership->nama }}</td>
                        <td class="px-4 py-3">{{ $membership->membershipTier->membership_tier ?? '-' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($membership->pembaruan_terakhir)->format('d-m-Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($membership->kadaluarsa)->format('d-m-Y') }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('membership.edit', $membership->id) }}" class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ route('membership.destroy', $membership->id) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus membership ini?')">
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
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Data membership kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4 text-center">
        {{ $memberships->links() }}
    </div>
@endsection
