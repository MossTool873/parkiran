@extends('components.app')

@section('title', 'Tambah User')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah User</h1>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 w-full">
        <form action="{{ url('/admin/users') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Username --}}
            <div>
                <label class="block text-sm font-medium mb-1">Username</label>
                <input
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nama</label>
                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama') }}"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input
                    type="password"
                    name="password"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium mb-1">Role</label>
                <select
                    name="role_id"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->role }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Button --}}
            <div class="flex gap-2 pt-4">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
                >
                    Simpan
                </button>

                <a
                    href="{{ url('/admin/users') }}"
                    class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection