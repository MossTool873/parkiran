@extends('components.app')

@section('title', 'Ganti Password')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <h1 class="text-2xl font-bold mb-6">Ganti Password</h1>

    {{-- Error --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Success --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 max-w-xl mx-auto">
        <form method="POST" action="{{ url('/ganti-password') }}" class="space-y-4" onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
            @csrf

            {{-- Username --}}
            <div>
                <label class="block text-sm font-medium mb-1">Username</label>
                <input
                    type="text"
                    value="{{ auth()->user()->username }}"
                    class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                    readonly
                >
            </div>

            {{-- Password Lama --}}
            <div>
                <label class="block text-sm font-medium mb-1">Password Lama</label>
                <input
                    type="password"
                    name="password_lama"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                    required
                >
            </div>

            {{-- Password Baru --}}
            <div>
                <label class="block text-sm font-medium mb-1">Password Baru</label>
                <input
                    type="password"
                    name="password"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                    required
                >
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                    required
                >
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
                    href="{{ url()->previous() }}"
                    class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
