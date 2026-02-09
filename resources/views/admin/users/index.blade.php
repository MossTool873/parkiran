@extends('components.app')

@section('title', 'Manajemen User')

@section('content')
    {{-- HEADER: judul & tombol Tambah --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4">
        <h1 class="text-2xl font-bold">Manajemen User</h1>

        <a href="{{ url('/admin/users/create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah User
        </a>
    </div>

    {{-- SEARCH BAR --}}
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ url('/admin/users') }}">
            <div class="flex gap-2 w-full md:w-80">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama user..."
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

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $user->username }}</td>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->role->role ?? '-' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ url('/admin/users/'.$user->id.'/edit') }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ url('/admin/users/'.$user->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus user ini?')">
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
                            Data user kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-center">
        {{ $users->links() }}
    </div>
@endsection
